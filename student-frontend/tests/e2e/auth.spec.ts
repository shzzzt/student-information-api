import { expect, test } from '@playwright/test'
import { mockAuthentication } from './helpers'

test('submits valid credentials and redirects to the dashboard', async ({ page }) => {
  await mockAuthentication(page, null)
  await page.goto('/login')

  await page.getByLabel('Email').fill('admin@example.com')
  await page.getByLabel('Password').fill('password')
  await page.getByRole('button', { name: 'Sign in' }).click()

  await expect(page).toHaveURL(/\/dashboard$/)
})

test('redirects an unauthenticated visitor from students to login', async ({ page }) => {
  await mockAuthentication(page, null)
  await page.goto('/students')

  await expect(page).toHaveURL(/\/login$/)
  await expect(page.getByRole('heading', { name: 'Sign in' })).toBeVisible()
})
