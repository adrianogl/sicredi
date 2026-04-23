const baseUrl = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8000/api/v1'

export interface ApiErrorBody {
  message?: string
  code?: string
  errors?: Record<string, string[]>
}

export class ApiError extends Error {
  readonly status: number
  readonly code?: string
  readonly errors?: Record<string, string[]>

  constructor(status: number, body: ApiErrorBody | null) {
    super(body?.message ?? `HTTP ${status}`)
    this.name = 'ApiError'
    this.status = status
    this.code = body?.code
    this.errors = body?.errors
  }
}

async function parseJsonSafe(response: Response): Promise<unknown> {
  const text = await response.text()
  if (text.length === 0) {
    return null
  }
  try {
    return JSON.parse(text)
  } catch {
    return null
  }
}

async function handleResponse<T>(response: Response): Promise<T> {
  const body = await parseJsonSafe(response)

  if (!response.ok) {
    throw new ApiError(response.status, body as ApiErrorBody | null)
  }

  return body as T
}

export const api = {
  async get<T>(path: string): Promise<T> {
    const response = await fetch(`${baseUrl}${path}`, {
      method: 'GET',
      headers: { Accept: 'application/json' },
      cache: 'no-store',
    })
    return handleResponse<T>(response)
  },

  async postUrl<T>(url: string, body: Record<string, unknown>): Promise<T> {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(body),
      cache: 'no-store',
    })
    return handleResponse<T>(response)
  },
}
