import { api, ApiError } from '../api'

function mockFetchOnce(init: { ok: boolean; status: number; body: string }) {
  const response = {
    ok: init.ok,
    status: init.status,
    text: jest.fn().mockResolvedValue(init.body),
  } as unknown as Response

  const spy = jest.fn().mockResolvedValue(response)
  global.fetch = spy as unknown as typeof fetch
  return spy
}

describe('api.get', () => {
  afterEach(() => {
    jest.resetAllMocks()
  })

  it('parses JSON when response is ok', async () => {
    mockFetchOnce({
      ok: true,
      status: 200,
      body: JSON.stringify({ hello: 'world' }),
    })

    const result = await api.get<{ hello: string }>('/hello')

    expect(result).toEqual({ hello: 'world' })
  })

  it('returns null when body is empty', async () => {
    mockFetchOnce({ ok: true, status: 204, body: '' })

    const result = await api.get('/empty')

    expect(result).toBeNull()
  })

  it('throws ApiError with message and code when body has JSON', async () => {
    mockFetchOnce({
      ok: false,
      status: 409,
      body: JSON.stringify({ message: 'dup', code: 'DUPLICATE_VOTE' }),
    })

    await expect(api.get('/x')).rejects.toMatchObject({
      name: 'ApiError',
      status: 409,
      code: 'DUPLICATE_VOTE',
      message: 'dup',
    })
  })

  it('falls back to "HTTP <status>" when body is non-JSON', async () => {
    mockFetchOnce({ ok: false, status: 502, body: '<html>Bad Gateway</html>' })

    await expect(api.get('/x')).rejects.toMatchObject({
      status: 502,
      message: 'HTTP 502',
      code: undefined,
    })
  })

  it('exposes validation errors when the backend returns them', async () => {
    mockFetchOnce({
      ok: false,
      status: 422,
      body: JSON.stringify({
        message: 'invalid',
        code: 'VALIDATION_FAILED',
        errors: { option: ['Opção inválida'] },
      }),
    })

    try {
      await api.get('/x')
      throw new Error('should have thrown')
    } catch (error) {
      expect(error).toBeInstanceOf(ApiError)
      expect((error as ApiError).errors).toEqual({
        option: ['Opção inválida'],
      })
    }
  })
})

describe('api.postUrl', () => {
  afterEach(() => {
    jest.resetAllMocks()
  })

  it('POSTs to the absolute URL with JSON body', async () => {
    const spy = mockFetchOnce({
      ok: true,
      status: 201,
      body: JSON.stringify({ data: { id: 7 } }),
    })

    const result = await api.postUrl('http://host/api/x', { a: 1 })

    expect(result).toEqual({ data: { id: 7 } })
    expect(spy).toHaveBeenCalledWith(
      'http://host/api/x',
      expect.objectContaining({
        method: 'POST',
        body: JSON.stringify({ a: 1 }),
      }),
    )
  })
})
