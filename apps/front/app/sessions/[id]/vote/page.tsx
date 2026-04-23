'use client'

import { use } from 'react'
import { FormScreen } from '@/components/screen/FormScreen'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { useScreenMessage } from '@/hooks/useScreenMessage'
import type { FormMessage } from '@/types/screen'

interface VotePageProps {
  params: Promise<{ id: string }>
}

export default function VotePage({ params }: VotePageProps) {
  const { id } = use(params)
  const { data, isLoading, error } = useScreenMessage<FormMessage>(
    `/ui/sessions/${id}/vote`,
  )

  if (isLoading) {
    return <p className="text-sm text-muted-foreground">Carregando formulário...</p>
  }

  if (error) {
    return (
      <Alert variant="destructive">
        <AlertDescription>{error.message}</AlertDescription>
      </Alert>
    )
  }

  if (!data) return null

  return <FormScreen message={data} />
}
