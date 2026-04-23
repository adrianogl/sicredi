'use client'

import { useRouter } from 'next/navigation'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useSubmitScreen } from '@/hooks/useSubmitScreen'
import type { SelectionMessage } from '@/types/screen'

interface SelectionScreenProps {
  message: SelectionMessage
}

interface SessionResponse {
  data: { id: number }
}

export function SelectionScreen({ message }: SelectionScreenProps) {
  const router = useRouter()
  const mutation = useSubmitScreen<SessionResponse>()

  const handleSelect = (
    url: string,
    body: Record<string, unknown> | unknown[] | undefined,
  ) => {
    const safeBody = body && !Array.isArray(body) ? body : {}
    mutation.mutate(
      { url, body: safeBody },
      {
        onSuccess: (response) => {
          router.push(`/sessions/${response.data.id}/vote`)
        },
      },
    )
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>{message.titulo}</CardTitle>
      </CardHeader>
      <CardContent className="flex flex-col gap-2">
        {mutation.error && (
          <Alert variant="destructive">
            <AlertDescription>{mutation.error.message}</AlertDescription>
          </Alert>
        )}
        {message.itens.map((item) => (
          <Button
            key={item.url}
            variant="outline"
            disabled={mutation.isPending}
            onClick={() => handleSelect(item.url, item.body)}
          >
            {item.texto}
          </Button>
        ))}
        {message.itens.length === 0 && (
          <p className="text-sm text-muted-foreground">Nenhuma pauta disponível no momento.</p>
        )}
      </CardContent>
    </Card>
  )
}
