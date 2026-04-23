import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { fireEvent, render, screen, waitFor } from '@testing-library/react'
import type { ReactNode } from 'react'
import { SelectionScreen } from '../SelectionScreen'
import { api } from '@/lib/api'
import type { SelectionMessage } from '@/types/screen'

jest.mock('@/lib/api', () => ({
  __esModule: true,
  api: { postUrl: jest.fn() },
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

const pushMock = jest.fn()
jest.mock('next/navigation', () => ({
  useRouter: () => ({ push: pushMock }),
}))

const mockedPost = api.postUrl as jest.MockedFunction<typeof api.postUrl>

function wrap({ children }: { children: ReactNode }) {
  const client = new QueryClient({
    defaultOptions: { queries: { retry: false }, mutations: { retry: false } },
  })
  return <QueryClientProvider client={client}>{children}</QueryClientProvider>
}

const message: SelectionMessage = {
  tipo: 'SELECAO',
  titulo: 'Selecione uma pauta',
  itens: [
    { texto: 'Pauta A', url: 'http://api.test/motions/1/sessions' },
    { texto: 'Pauta B', url: 'http://api.test/motions/2/sessions' },
  ],
}

describe('SelectionScreen', () => {
  afterEach(() => {
    jest.clearAllMocks()
  })

  it('renders the title and one button per item', () => {
    render(<SelectionScreen message={message} />, { wrapper: wrap })

    expect(screen.getByText('Selecione uma pauta')).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Pauta A' })).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Pauta B' })).toBeInTheDocument()
  })

  it('submits the item url and redirects to the vote page on success', async () => {
    mockedPost.mockResolvedValueOnce({ data: { id: 42 } })

    render(<SelectionScreen message={message} />, { wrapper: wrap })
    fireEvent.click(screen.getByRole('button', { name: 'Pauta A' }))

    await waitFor(() => expect(pushMock).toHaveBeenCalledWith('/sessions/42/vote'))
    expect(mockedPost).toHaveBeenCalledWith(
      'http://api.test/motions/1/sessions',
      {},
    )
  })

  it('renders an error alert when the submission fails', async () => {
    mockedPost.mockRejectedValueOnce(new Error('Falha'))

    render(<SelectionScreen message={message} />, { wrapper: wrap })
    fireEvent.click(screen.getByRole('button', { name: 'Pauta A' }))

    expect(await screen.findByText('Falha')).toBeInTheDocument()
    expect(pushMock).not.toHaveBeenCalled()
  })

  it('renders an empty-state message when there are no items', () => {
    render(
      <SelectionScreen
        message={{ tipo: 'SELECAO', titulo: 'Vazio', itens: [] }}
      />,
      { wrapper: wrap },
    )
    expect(
      screen.getByText('Nenhuma pauta disponível no momento.'),
    ).toBeInTheDocument()
  })
})
