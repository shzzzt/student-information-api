import { expect, test } from '@playwright/test'
import { login, paginated } from './helpers'

const students = [
  {
    id: 10,
    user_id: 110,
    student_number: '2026-0010',
    first_name: 'Maria',
    last_name: 'Dela Cruz',
    program_id: 3,
    year_level: 2,
    admission_date: '2025-08-01',
    status: 'active',
    program: { code: 'BSIT' },
  },
  {
    id: 11,
    user_id: 111,
    student_number: '2026-0011',
    first_name: 'Jose',
    last_name: 'Santos',
    program_id: 3,
    year_level: 1,
    admission_date: '2025-08-01',
    status: 'active',
    program: { code: 'BSIT' },
  },
]

test.beforeEach(async ({ page }) => {
  await page.route('**/students?**', async (route) => {
    const url = new URL(route.request().url())
    const search = url.searchParams.get('search')?.toLowerCase()
    const rows = search
      ? students.filter((student) => `${student.first_name} ${student.last_name}`.toLowerCase().includes(search))
      : students

    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify(paginated(rows)),
    })
  })

  await page.route('**/programs?**', async (route) => {
    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        success: true,
        message: 'Programs retrieved successfully.',
        data: [{ id: 3, code: 'BSIT', name: 'Information Technology', degree_level: 'bachelor', status: 'active' }],
      }),
    })
  })

  await page.route('**/students', async (route) => {
    if (route.request().method() !== 'POST') {
      await route.continue()
      return
    }

    const submitted = route.request().postDataJSON()
    await route.fulfill({
      status: 201,
      contentType: 'application/json',
      body: JSON.stringify({
        success: true,
        message: 'Student created successfully.',
        data: { id: 99, ...submitted },
      }),
    })
  })

  await page.route('**/students/99', async (route) => {
    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        success: true,
        message: 'Student retrieved successfully.',
        data: { id: 99, ...students[0] },
      }),
    })
  })

  await login(page, 'admin')
})

test('filters student rows by search text', async ({ page }) => {
  await page.getByRole('link', { name: 'Students' }).click()
  await expect(page.getByRole('link', { name: 'Maria Dela Cruz' })).toBeVisible()
  await expect(page.getByRole('link', { name: 'Jose Santos' })).toBeVisible()

  await page.getByLabel('Search').fill('dela')

  await expect(page.getByRole('link', { name: 'Maria Dela Cruz' })).toBeVisible()
  await expect(page.getByRole('link', { name: 'Jose Santos' })).not.toBeVisible()
})

test('creates a student and redirects to the detail page', async ({ page }) => {
  await page.goto('/students')
  await page.getByRole('link', { name: '+ New Student' }).click()

  await page.getByLabel('Student number').fill('2026-0099')
  await page.getByLabel('First name').fill('Lina')
  await page.getByLabel('Last name').fill('Mendoza')
  await page.getByLabel('Program').selectOption('3')
  await page.getByLabel('Year level').fill('1')
  await page.getByLabel('Gender').selectOption('prefer_not_to_say')
  await page.getByLabel('Admission date').fill('2026-08-01')
  await page.getByRole('button', { name: 'Create student' }).click()

  await expect(page).toHaveURL(/\/students\/99$/)
})
