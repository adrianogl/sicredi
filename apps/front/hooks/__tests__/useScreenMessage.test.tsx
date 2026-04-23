import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { renderHook, waitFor } from '@testing-library/react'
import type { ReactNode } from 'react'
import { useScreenMessage } from '../useScreenMessage'
import { api } from '@/lib/api'

jest.mock('@/lib/api', () => ({
  __esModule: true,
  api: { get: jest.fn() },
  ApiError: class ApiError extends Error {
    status: number
    code?: string
    constructor(status: number, message: string, code?: string) {
      super(message)
      this.status = status
      this.code = code
    }
  },
}))

function wrap({ children }: { children: ReactNode }) {
  const client = new QueryClient({
    defaultOptions: { queries: { retry: false } },
  })
  return <QueryClientProvider client={client}>{children}</QueryClientProvider>
}

const mockedGet = api.get as jest.MockedFunction<typeof api.get>

describe('useScreenMessage', () => {
  afterEach(() => {
    jest.resetAllMocks()
  })

  it('returns the screen message when the request succeeds', async () => {
    const payload = {
      tipo: 'SELECAO' as const,
      titulo: 'Pautas',
      itens: [],
    }
    mockedGet.mockResolvedValueOnce(payload)

    const { result } = renderHook(() => useScreenMessage('/ui/motions'), {
      wrapper: wrap,
    })

    await waitFor(() => expect(result.current.isSuccess).toBe(true))
    expect(result.current.data).toEqual(payload)
    expect(mockedGet).toHaveBeenCalledWith('/ui/motions')
  })

  it('does not fire when path is null', () => {
    renderHook(() => useScreenMessage(null), { wrapper: wrap })
    expect(mockedGet).not.toHaveBeenCalled()
  })
})
