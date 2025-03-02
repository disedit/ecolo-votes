export default {
  global: {
    loading: 'Loading...',
    close: 'Close',
    close_modal: 'Close modal',
  },
  nav: {
    user: 'User',
    logout: 'Log out'
  },
  menu: {
    vote: 'Vote',
    results: 'Results'
  },
  voter: {
    status: {
      connected: 'Connecté',
      disconnected: 'Disconnected',
      connecting: 'Connecting...',
      reconnecting: 'Reconnecting...',
      unavailable: 'Unavailable',
      failed: 'Failed',
      initialized: 'Initialized',
      ongoing: 'Vote ongoing...',
      ongoing_short: 'Ongoing',
      pending: 'Vote pending...',
      pending_short: 'Pending',
      upcoming: 'Vote upcoming...',
      upcoming_short: 'Upcoming'
    },
    form: {
      select_max: 'Select one option | Select up to {max} options',
      button: 'Vote',
      button_long: 'Cast your voe',
      secret: 'Your vote is secret',
      unselect: 'Unselect an option to select this option'
    },
    standby: {
      title: 'En attente, les solutions vertes sont en cours de préparation...',
      message: 'Les votes apparaîtront sur cet écran au fur et à mesure de leur ouverture'
    },
    announcer: {
      new_vote: 'New vote just opened',
      vote_closed: 'Vote just closed',
      check_results: 'Check the screen for results'
    },
    confirm: {
      title: 'Confirm your vote',
      empty: 'Select at least one option to vote',
      code: 'Enter the code displayed on screen',
      button: 'Cast vote',
      submitting: 'Submitting...'
    },
    debate: {
      available_options:  'Available options:',
      vote_will_open: 'Voting will open shortly...',
      concluded: 'Voting concluded.'
    },
    reveal: {
      hide: 'Click to hide',
      reveal: 'Click to reveal'
    },
    submitted: {
      title: 'Your vote has been submitted.'
    }
  },
  results: {
    page: {
      title: 'Results',
      columns: {
        vote: 'Vote',
        result: 'Result'
      },
      more: 'Show results'
    },
    stats: {
      codes_in_use: 'Codes in use',
      codes_voted: 'Codes voted',
      percenage: 'Percentage',
      votes_cast: 'Votes cast',
      majority_needed: 'Majority needed',
      to_select: 'To select' // Ex: To select: 5
    }
  },
  badge: {
    welcome: {
      F: 'Welcome',
      M: 'Welcome',
      O: 'Welcome',
    },
    welcome_long: {
      F: ' Welcome to the 39th Congress of the European Green Party',
      M: '',
      O: '',
    },
    checked_in: 'You have checked in',
    show_ticket: 'Show this ticket at the entrance to check in'
  },
  code_login: {
    label: 'Enter your code',
    button: 'Enter',
    submitting: 'Submitting...'
  },
  login: {
    instructions: 'If you have registered for the congress, enter your email below to access the platform.',
    submitted: 'If the email address was on our database, you will receive an email with a link to access the platform.',
    check_inbox: 'Check your inbox',
    email: 'Email address',
    email_placeholder: 'Enter your email',
    button: 'Send me a link',
    submitting: 'Submitting...'
  },
  inputs: {
    required: '(required)'
  },
  options: {
    yes: 'Yes',
    no: 'No',
    abstain: 'Abstain'
  },
  admin: {
    title: 'Admin',
    dashboard: {
      access_control: 'Access control',
      codes: 'Codes',
      vote_manager: 'Vote manager',
      screen: 'Screen',
      badge_scanner: 'Badge scanner',
    },
    votes: {
      nav: {
        title: 'Vote manager'
      },
      actions: {
        create: 'Create vote',
        stop: 'Stop',
        results: 'Results',
        next_up: 'Next up',
        open: 'Open',
        reopen: 'Reopen',
        open_vote: 'Open vote',
        close: 'Vote',
        highlight: 'Highlight',
        stop_highlighting: 'Stop highlighting',
        close_debate: 'Close debate',
        close_vote: 'Close vote',
        duplicate: 'Duplicate',
        reorder: 'Reorder',
        delete: 'Delete vote',
        move_up: 'Move up',
        move_down: 'Move down',
        finish_reordering: 'Finish reordering',
        remove: 'Remove',
        add_option: 'Add option',
        load_options: 'Load options',
        maximize: 'Maximize',
        minimize: 'Minimize',
        hide: 'Hide'
      },
      columns: {
        name: 'Name',
        majority: 'Majority',
        max: 'Max.',
        result: 'Result',
        closed_at: 'Closed at',
        actions: 'Actions',
      },
      search: {
        placeholder: 'Search this table'
      },
      warning: 'Reopening a vote will recalculate vote allocation and may alter the results.',
      results: {
        no_winner: 'No winner',
      },
      status: {
        debating: 'Debating...',
        ongoing: 'Ongoing...',
      },
      create: {
        options: 'Options',
        required: '(required)'
      },
      ongoing: {
        title: 'Ongoing vote'
      }
    },
    scanner: {
      badge_scanner: 'Badge scanner',
      badges: 'Badges',
      codes: 'Vote codes'
    },
    codes: {
      title: 'Codes',
      actions: {
        create: 'Create codes',
        print: 'Print codes',
        activate: 'Activate',
        deactivate: 'Deactivate',
      },
      search: {
        placeholder: 'Search this table'
      },
      columns: {
        code: 'Code',
        pickedup_at: 'Picked up at',
        used_at: 'Used at',
        actions: 'Actions',
      },
      stats: {
        total: 'total', // Ex: 10 total,
        picked_up: 'picked up', // Ex: 7 picked up,
        used: 'used', // Ex: 5 used,
      },
      create: {
        title: 'Create codes',
        amount: 'Amount of codes to generate',
        generating: 'Generating codes...',
        generate: 'Generate codes',
      }
    },
    credentials: {
      title: 'Access control',
      search: {
        placeholder: 'Search this table'
      },
      columns: {
        first_name: 'First Name',
        last_name: 'Last Name',
        type: 'Type',
        group: 'Group',
        checked_in: 'Checked in',
        actions: 'Actions',
      },
      stats: {
        total: 'total', // Ex: 10 total
        checked_in: 'checked in', // Ex: 7 checked in
      },
      actions: {
        scan: 'Scan badges',
        check_in: 'Check in',
        details: 'Details',
        access_log: 'Access log'
      }
    }
  }
}