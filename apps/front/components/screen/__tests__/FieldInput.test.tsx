import { fireEvent, render, screen } from '@testing-library/react'
import { FieldInput } from '../FieldInput'
import type { FormField } from '@/types/screen'

function setup(field: FormField) {
  const onChange = jest.fn()
  render(<FieldInput field={field} value="" onChange={onChange} />)
  return { onChange }
}

describe('FieldInput', () => {
  it('renders a text input for TEXTO', () => {
    setup({ tipo: 'TEXTO', label: 'Nome', id: 'nome' })
    const input = screen.getByLabelText('Nome')
    expect(input).toHaveAttribute('type', 'text')
  })

  it('renders a number input for NUMERO', () => {
    setup({ tipo: 'NUMERO', label: 'Idade', id: 'idade' })
    expect(screen.getByLabelText('Idade')).toHaveAttribute('type', 'number')
  })

  it('renders a date input for DATA', () => {
    setup({ tipo: 'DATA', label: 'Nascimento', id: 'nasc' })
    expect(screen.getByLabelText('Nascimento')).toHaveAttribute('type', 'date')
  })

  it('calls onChange with the typed value', () => {
    const { onChange } = setup({ tipo: 'TEXTO', label: 'Nome', id: 'nome' })
    fireEvent.change(screen.getByLabelText('Nome'), { target: { value: 'abc' } })
    expect(onChange).toHaveBeenCalledWith('abc')
  })

  it('respects the disabled prop', () => {
    render(
      <FieldInput
        field={{ tipo: 'TEXTO', label: 'Nome', id: 'nome' }}
        value=""
        onChange={() => {}}
        disabled
      />,
    )
    expect(screen.getByLabelText('Nome')).toBeDisabled()
  })
})
