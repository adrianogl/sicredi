import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { fireEvent, render, screen, waitFor } from '@testing-library/react'
import type { ReactNode } from 'react'
import { FormScreen } from '../FormScreen'
import { api } from '@/lib/api'
import type { FormMessage } from '@/types/screen'

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

const message: FormMessage = {
  tipo: 'FORMULARIO',
  titulo: 'Votar',
  itens: [{ tipo: 'TEXTO', label: 'ID do associado', id: 'member_id' }],
  botoes: [
    {
      texto: 'Cancelar',
      url: 'http://sicredi.test:3000/motions',
      metodo: 'GET',
    },
    { texto: 'Yes', url: 'http://api.test/sessions/1/votes?option=Yes' },
    { texto: 'No', url: 'http://api.test/sessions/1/votes?option=No' },
  ],
}

describe('FormScreen', () => {
  afterEach(() => {
    jest.clearAllMocks()
  })

  it('renders title, field, and buttons', () => {
    render(<FormScreen message={message} />, { wrapper: wrap })
    expect(screen.getByText('Votar')).toBeInTheDocument()
    expect(screen.getByLabelText('ID do associado')).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Cancelar' })).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Yes' })).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'No' })).toBeInTheDocument()
  })

  it('sends the filled values when a submit button is clicked', async () => {
    mockedPost.mockResolvedValueOnce({ data: { id: 1 } })

    render(<FormScreen message={message} />, { wrapper: wrap })

    fireEvent.change(screen.getByLabelText('ID do associado'), {
      target: { value: '12345678900' },
    })
    fireEvent.click(screen.getByRole('button', { name: 'Yes' }))

    await waitFor(() =>
      expect(mockedPost).toHaveBeenCalledWith(
        'http://api.test/sessions/1/votes?option=Yes',
        { member_id: '12345678900' },
      ),
    )
  })

  it('shows success message after a successful submission', async () => {
    mockedPost.mockResolvedValueOnce({ data: { id: 1 } })

    render(<FormScreen message={message} />, { wrapper: wrap })
    fireEvent.click(screen.getByRole('button', { name: 'Yes' }))

    expect(
      await screen.findByText('Voto registrado com sucesso.'),
    ).toBeInTheDocument()
    expect(screen.getByRole('link', { name: 'Voltar para lista' })).toHaveAttribute(
      'href',
      '/motions',
    )
  })

  it('shows the error message when the submission fails', async () => {
    mockedPost.mockRejectedValueOnce(new Error('Sessão fechada'))

    render(<FormScreen message={message} />, { wrapper: wrap })
    fireEvent.click(screen.getByRole('button', { name: 'Yes' }))

    expect(await screen.findByText('Sessão fechada')).toBeInTheDocument()
  })

  it('invokes onSuccess instead of the default alert when provided', async () => {
    const response = { data: { id: 99 } }
    mockedPost.mockResolvedValueOnce(response)
    const onSuccess = jest.fn()

    render(<FormScreen message={message} onSuccess={onSuccess} />, {
      wrapper: wrap,
    })
    fireEvent.click(screen.getByRole('button', { name: 'Yes' }))

    await waitFor(() => expect(onSuccess).toHaveBeenCalled())
    const [data, variables] = onSuccess.mock.calls[0]
    expect(data).toEqual(response)
    expect(variables).toEqual({
      url: 'http://api.test/sessions/1/votes?option=Yes',
      body: { member_id: '' },
    })
    expect(screen.queryByText('Voto registrado com sucesso.')).toBeNull()
  })

  it('navigates without POSTing when the button has metodo=GET', () => {
    render(<FormScreen message={message} />, { wrapper: wrap })

    fireEvent.click(screen.getByRole('button', { name: 'Cancelar' }))

    expect(pushMock).toHaveBeenCalledWith('/motions')
    expect(mockedPost).not.toHaveBeenCalled()
  })
})
