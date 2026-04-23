export interface SelectionItem {
  texto: string
  url: string
  // Backend serializes empty PHP arrays as JSON `[]`. We accept both.
  body?: Record<string, unknown> | unknown[]
}

export interface SelectionMessage {
  tipo: 'SELECAO'
  titulo: string
  itens: SelectionItem[]
}

export type FieldKind = 'TEXTO' | 'NUMERO' | 'DATA'

export interface FormField {
  tipo: FieldKind
  label: string
  id: string
}

export interface FormButton {
  texto: string
  url: string
  // 'GET' sinaliza botão de navegação (cancelar/voltar) — cliente só troca de
  // tela. Ausência implica POST (comportamento padrão do Anexo 1).
  metodo?: 'GET' | 'POST'
}

export interface FormMessage {
  tipo: 'FORMULARIO'
  titulo: string
  itens: FormField[]
  botoes: FormButton[]
}

export type ScreenMessage = SelectionMessage | FormMessage
