'use client'

import { useQuery } from '@tanstack/react-query'
import { api, ApiError } from '@/lib/api'
import type { ScreenMessage } from '@/types/screen'

export function useScreenMessage<T extends ScreenMessage = ScreenMessage>(
  path: string | null,
) {
  return useQuery<T, ApiError>({
    queryKey: ['screen', path],
    queryFn: () => api.get<T>(path!),
    enabled: path !== null,
  })
}
