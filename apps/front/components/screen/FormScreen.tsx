'use client'

import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { useState } from 'react'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
  useSubmitScreen,
  type SubmitScreenVariables,
} from '@/hooks/useSubmitScreen'
import type { FormButton, FormMessage } from '@/types/screen'
import { FieldInput } from './FieldInput'

interface FormScreenProps {
  message: FormMessage
  onSuccess?: (response: unknown, variables: SubmitScreenVariables) => void
}

export function FormScreen({ message, onSuccess }: FormScreenProps) {
  const router = useRouter()
  const [values, setValues] = useState<Record<string, string>>(() =>
    Object.fromEntries(message.itens.map((field) => [field.id, ''])),
  )
  const mutation = useSubmitScreen()

  const handleClick = (button: FormButton) => {
    if (button.metodo === 'GET') {
      try {
        const pathname = new URL(button.url).pathname
        router.push(pathname)
      } catch {
        router.push(button.url)
      }
      return
    }
    mutation.mutate({ url: button.url, body: values }, { onSuccess })
  }

  const disabled = mutation.isPending || mutation.isSuccess

  return (
    <Card>
      <CardHeader>
        <CardTitle>{message.titulo}</CardTitle>
      </CardHeader>
      <CardContent className="flex flex-col gap-4">
        {message.itens.map((field) => (
          <FieldInput
            key={field.id}
            field={field}
            value={values[field.id] ?? ''}
            onChange={(value) =>
              setValues((prev) => ({ ...prev, [field.id]: value }))
            }
            disabled={disabled}
          />
        ))}

        {mutation.error && (
          <Alert variant="destructive">
            <AlertDescription>{mutation.error.message}</AlertDescription>
          </Alert>
        )}

        {!onSuccess && mutation.isSuccess && (
          <Alert className="border-green-500/50 text-green-700 dark:text-green-400">
            <AlertDescription className="flex flex-wrap items-center gap-2">
              <span>Voto registrado com sucesso.</span>
              <Link href="/motions" className="underline">
                Voltar para lista
              </Link>
            </AlertDescription>
          </Alert>
        )}

        <div className="flex flex-wrap gap-2">
          {message.botoes.map((button) => (
            <Button
              key={button.url}
              onClick={() => handleClick(button)}
              variant={button.metodo === 'GET' ? 'outline' : 'default'}
              disabled={disabled && button.metodo !== 'GET'}
            >
              {button.texto}
            </Button>
          ))}
        </div>
      </CardContent>
    </Card>
  )
}
