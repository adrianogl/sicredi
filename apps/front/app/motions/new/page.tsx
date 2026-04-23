'use client'

import { useQueryClient } from '@tanstack/react-query'
import { useRouter } from 'next/navigation'
import { FormScreen } from '@/components/screen/FormScreen'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { useScreenMessage } from '@/hooks/useScreenMessage'
import type { FormMessage } from '@/types/screen'

export default function NewMotionPage() {
  const router = useRouter()
  const queryClient = useQueryClient()
  const { data, isLoading, error } = useScreenMessage<FormMessage>('/ui/motions/new')

  const handleSuccess = () => {
    queryClient.invalidateQueries({ queryKey: ['screen', '/ui/motions'] })
    router.push('/motions')
  }

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

  return <FormScreen message={data} onSuccess={handleSuccess} />
}
