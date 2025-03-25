export default {
  global: {
    loading: 'Loading...',
    close: 'Close',
    close_modal: 'Close modal',
    cancel: 'Cancel',
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
      button_long: 'Cast your vote',
      secret: 'Your vote is secret',
      unselect: 'Unselect an option to select this option'
    },
    standby: {
      title: 'Attendez, pas de vote en cours...',
      message: 'Votes will show up here as they open'
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
    },
    your_vote: 'Your vote',
  },
  badge: {
    welcome: {
      F: 'Welcome',
      M: 'Welcome',
      O: 'Welcome',
    },
    welcome_long: {
      F: 'Welcome to {name}',
      M: 'Welcome to {name}',
      O: 'Welcome to {name}',
    },
    checked_in: 'You have checked in',
    show_ticket: 'Show this ticket at the entrance to check in'
  },
  code_login: {
    title: 'Access',
    instructions: 'Enter the number shown on the piece of paper that you picked up to vote',
    instructions_qr: 'Scan the QR on the piece of paper that you picked up to vote',
    code: 'Code',
    button: 'Enter',
    scan_qr: 'Scan QR code',
    submitting: 'Submitting...',
    code_invalid: 'Invalid code',
    or: 'or',
  },
  login: {
    instructions: 'To log in, enter your email address below.',
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
  majorities: {
    '50': '>50% absolue',
    '2/3': '≥2/3 deux tiers',
    simple: 'Simple'
  },
  genders: {
    F: 'Female',
    M: 'Male',
    O: 'Non-binary',
    N: 'N/A'
  },
  print: {
    codes: {
      instructions: 'Please, scan the QR code above the access the voting application.',
      alternative: 'Alternatively, you can go to {0} and enter your ballot reference: {1}',
      do_not_share: 'Do not share this code with anybody'
    }
  },
  screen: {
    votes_cast: 'Votes cast',
    turnout: 'Turnout',
    next_up: 'Next up',
    ongoing: 'Vote ongoing',
    results: 'Results',
    required_votes: '(≥{required} votes)'
  },
  footer: {
    privacy_policy: 'Mentions légales et protection de la vie privée'
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
      options: {
        label: 'Options',
        required: '(required)'
      },
      ongoing: {
        title: 'Ongoing vote',
        just_closed: 'Just closed'
      },
      importer: {
        title: 'Load options form another vote',
        fields: {
          select_vote: 'Select a vote to copy options from',
        },
        button: 'Import options',
      },
      create: {
        title: 'Create new vote',
        fields: {
          title: 'Title',
          subtitle: 'Subtitle',
          subtitle_placeholder: 'For example, 2nd round',
          type: 'Type of vote',
          types: {
            yesno: 'Yes / No / Abstain',
            options: 'Candidates / Custom options'
          },
          majority: 'Threshold',
          abstentions: 'Abstentions',
          abstention_options: {
            with: 'avec abstentions',
            without: 'sans les abstentions'
          },
          max_votes: 'Max. to select',
          relative_to: 'Majority relative to',
          relative_to_options: {
            turnout: 'Personnes qui ont voté',
            votes_cast: 'Voix reçues'
          },
          secret: 'Hide selected option',
          open_immediately: 'Open vote immediately upon creation'
        }
      },
      delete: {
        title: 'Delete vote',
        warning: 'Are you sure you want to delete this vote? All ballots cast will also be removed.',
        button: 'Delete vote'
      },
    },
    scanner: {
      badge_scanner: 'Badge scanner',
      auto: 'Auto',
      badges: 'Badges',
      codes: 'Vote codes'
    },
    codes: {
      title: 'Codes',
      actions: {
        create: 'Create codes',
        print: 'Print codes',
        activate: 'Activate',
        activated: 'Activated',
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
        preactivate: 'Preactivate codes'
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
        email: 'Email',
        phone: 'Phone',
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
      },
      import: {
        title: 'Import voters',
        instructions: 'Download the sample file and enter the voter data. Then, upload it here to import the voters.',
        formats: 'Accepted formats: .csv, .xls, .xlsx, .ods',
        download_sample: 'Sample file (.xlsx)',
        file: 'File',
        wipe: 'Delete and replace current voters (if any)',
        action: 'Upload',
      },
      notify: {
        title: 'Send badges to participants',
        mail_notification_subject: 'E-mail notification subject',
        mail_notification_body: 'E-mail notification body',
        help: '',
        sms: 'Also send an SMS',
        only_unnotified: 'Exclude participants that have been notified before.',
        sms_notification: 'SMS notification',
        save: 'Save',
        saving: 'Saving...',
        send: 'Send',
        sending: 'Sending...',
        saved: 'Notification saved.',
        sent: 'Notification being sent to all participants. This process may take a few minutes. You may close this window.',
        close: 'Close'
      }
    }
  }
}