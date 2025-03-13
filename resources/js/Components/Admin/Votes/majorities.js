const majorityDictionary = {
  '50': '>50% absolue',
  '2/3': '≥2/3 deux tiers',
  simple: 'Simple',
}

export const majorities = majorityDictionary

export const majoritiesOnScreen = {
  '50': '>50%',
  '2/3': '≥2/3',
  simple: 'Simple',
}

export const thresholds = {
  simple: null,
  '50': 1/2,
  '2/3': 2/3,
}


// Remove
export const percentages = {
  simple: 'votes_cast',
  '50_with_abs': 'votes_cast',
  '50_without_abs': 'votes_cast',
  '2/3_with_abs': 'votes_cast',
  '2/3_without_abs': 'votes_cast',
}

export const majorityName = (vote) => {
  let name = majoritiesOnScreen[vote.majority]
  if (vote.majority !== 'simple') {
    name += vote.relative_to === 'turnout' ? ' des personnes' : ' des voix'
    name += vote.with_abstentions ? ' avec abstentions' : ' sans les abstentions'
  }
  return name
}