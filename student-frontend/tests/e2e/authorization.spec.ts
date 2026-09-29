import { expect, test } from '@playwright/test'
import { login } from './helpers'

test('student users cannot see or directly visit the students area', async ({ page }) => {
  await login(page, 'student')

  await expect(page.getByRole('navigation').getByRole('link', { name: 'Students' })).toHaveCount(0)

  await page.goto('/students')

  await expect(page).toHaveURL(/\/forbidden$/)
  await expect(page.getByRole('heading', { name: /forbidden/i })).toBeVisible()
})
