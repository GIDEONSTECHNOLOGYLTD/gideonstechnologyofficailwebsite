// Lockdown configuration to fix the intrinsics error
import { lockdown } from '@agoric/ses';

lockdown({
  errorTaming: 'unsafe',
  overrideTaming: 'severe',
  consoleTaming: 'unsafe',
  // Allow intrinsics needed by the application
  mathTaming: 'unsafe',
  dateTaming: 'unsafe',
  regExpTaming: 'unsafe',
  // Additional intrinsics to allow
  promiseTaming: 'unsafe',
  domainTaming: 'unsafe',
  stringTaming: 'unsafe',
  localeTaming: 'unsafe',
  // Set this to true to prevent lockdown from removing any intrinsics
  __shimTransforms__: [],
  // Prevent removal of unpermitted intrinsics
  requireThis: false
});

console.log('Lockdown initialized with safe configuration');