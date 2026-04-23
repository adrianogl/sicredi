'use client'

import Link from 'next/link'
import { SelectionScreen } from '@/components/screen/SelectionScreen'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { buttonVariants } from '@/components/ui/button'
import { useScreenMessage } from '@/hooks/useScreenMessage'
import type { SelectionMessage } from '@/types/screen'

export default function MotionsPage() {
  const { data, isLoading, error } = useScreenMessage<SelectionMessage>('/ui/motions')

  return (
    <>
      <div className="flex justify-end">
        <Link
          href="/motions/new"
          className={buttonVariants({ variant: 'secondary' })}
        >
          Nova pauta
        </Link>
      </div>

      {isLoading && (
        <p className="text-sm text-muted-foreground">Carregando pautas...</p>
      )}

      {error && (
        <Alert variant="destructive">
          <AlertDescription>{error.message}</AlertDescription>
        </Alert>
      )}

      {data && <SelectionScreen message={data} />}
    </>
  )
}
