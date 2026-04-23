'use client'

import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { FormField } from '@/types/screen'

interface FieldInputProps {
  field: FormField
  value: string
  onChange: (value: string) => void
  disabled?: boolean
}

const fieldTypeToHtml: Record<FormField['tipo'], 'text' | 'number' | 'date'> = {
  TEXTO: 'text',
  NUMERO: 'number',
  DATA: 'date',
}

export function FieldInput({ field, value, onChange, disabled }: FieldInputProps) {
  return (
    <div className="grid gap-2">
      <Label htmlFor={field.id}>{field.label}</Label>
      <Input
        id={field.id}
        type={fieldTypeToHtml[field.tipo]}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        disabled={disabled}
      />
    </div>
  )
}
