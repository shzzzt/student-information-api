import { expect, test } from '@playwright/test'

const email = process.env.E2E_EMAIL
const password = process.env.E2E_PASSWORD
const runLiveApiTests = process.env.E2E_API_TESTS === '1'

test.describe('frontend against the real Laravel API', () => {
  test.skip(!runLiveApiTests, 'Set E2E_API_TESTS=1 after starting and seeding the Laravel API.')

  test('authenticates and displays live dashboard data', async ({ page }, testInfo) => {
    test.skip(!email || !password, 'Set E2E_EMAIL and E2E_PASSWORD to a seeded account.')
    await page.goto('/login')
    await page.getByLabel('Email').fill(email!)
    await page.getByLabel('Password').fill(password!)
    await page.getByRole('button', { name: 'Sign in' }).click()

    await expect(page).toHaveURL(/\/dashboard$/)
    await expect(page.getByRole('heading', { name: /^Welcome,/ })).toBeVisible()
    await expect(page.getByText('Some dashboard counts could not be loaded.')).toHaveCount(0)
    await expect(page.getByRole('main').getByText('Students', { exact: true })).toBeVisible()
    await testInfo.attach('live-dashboard', { body: await page.screenshot({ fullPage: true }), contentType: 'image/png' })
  })

  test('shows the backend validation response for invalid login', async ({ page }) => {
    await page.goto('/login')
    await page.getByLabel('Email').fill('not-a-real-user@example.com')
    await page.getByLabel('Password').fill('incorrect-password')
    await page.getByRole('button', { name: 'Sign in' }).click()
    await expect(page.getByRole('alert')).toBeVisible()
  })

  test('opens the responsive navigation and loads the live enrollment list', async ({ page }, testInfo) => {
    test.skip(!email || !password, 'Set E2E_EMAIL and E2E_PASSWORD to a seeded account.')
    await page.setViewportSize({ width: 390, height: 844 })
    await page.goto('/login')
    await page.getByLabel('Email').fill(email!)
    await page.getByLabel('Password').fill(password!)
    await page.getByRole('button', { name: 'Sign in' }).click()
    await expect(page).toHaveURL(/\/dashboard$/)
    await page.getByRole('button', { name: 'Open navigation' }).click()
    await page.getByRole('navigation', { name: 'Main navigation' }).getByRole('link', { name: 'Enrollments' }).click()
    await expect(page).toHaveURL(/\/enrollments$/)
    await expect(page.getByRole('heading', { name: 'Enrollments' })).toBeVisible()
    await expect(page.getByText('No enrollments found')).toHaveCount(0)
    await testInfo.attach('live-enrollments-mobile', { body: await page.screenshot({ fullPage: true }), contentType: 'image/png' })
  })
})
