'use client'

import { useMutation } from '@tanstack/react-query'
import { api, ApiError } from '@/lib/api'

export interface SubmitScreenVariables {
  url: string
  body: Record<string, unknown>
}

export function useSubmitScreen<TResponse = unknown>() {
  return useMutation<TResponse, ApiError, SubmitScreenVariables>({
    mutationFn: ({ url, body }) => api.postUrl<TResponse>(url, body),
  })
}
