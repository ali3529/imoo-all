function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '/', name: 'welcome', component: page('welcome.vue') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  { path: '/home', name: 'home', component: page('home.vue') },
  {
    path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') },
      { path: 'verify/phone', name: 'settings.verifyPhone', component: page('settings/phoneVerify.vue') }
    ]
  },

  { path: '/properties/create', name: 'properties.create', component: page('properties/create') },
  { path: '/properties/:reference/details', name: 'properties.details', component: page('properties/details') },
  { path: '/properties/:reference/details/step2', name: 'properties.details2', component: page('properties/details2') },
  { path: '/properties/:reference/details/step3', name: 'properties.details3', component: page('properties/details3') },
  { path: '/properties/:reference/details/confirmation', name: 'properties.confirmation', component: page('properties/confirmation') },
  { path: '/properties/:reference/details/failed', name: 'properties.failed', component: page('properties/failed') },
  { path: '/properties/my', name: 'properties.my', component: page('properties/list') },

  { path: '*', component: page('errors/404.vue') }
]
