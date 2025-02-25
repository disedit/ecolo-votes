export function formatCurrency(number) {
  return new Intl.NumberFormat('en-IE', { style: 'currency', currency: 'EUR' }).format(
    number,
  )
}