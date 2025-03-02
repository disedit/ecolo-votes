export function optionClasses(option, vote) {
  return {
    'option-no': option.is_no,
    'option-abstain': option.is_abstain,
    'option-yes': vote.type === 'yesno' && !option.is_no && !option.is_abstain
  }
}
