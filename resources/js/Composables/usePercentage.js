export function formatPercentage(value) {
  if (isNaN(value)) value = 0
  value = Math.round((value + Number.EPSILON) * 10000) / 100
  return String(value) + '%'
}