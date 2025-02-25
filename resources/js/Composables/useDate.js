import { format } from 'date-fns'

export function formatDate(date) {
  return format(new Date(date), "dd/MM/yyyy")
}

export function formatDateTime(date) {
  return format(new Date(date), "dd/MM/yy HH:mm")
}

export function formatWeekTime(date) {
  return format(new Date(date), "EEE HH:mm")
}