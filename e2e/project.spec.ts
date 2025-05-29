import { test, expect } from '@playwright/test';

test('Projects can be created,edited and deleted properly', async ({ page }) => {
  await page.goto('http://localhost:8000/login');
  await page.getByRole('textbox', { name: 'Email address' }).fill('tbattur22@yahoo.com');
  await page.getByRole('textbox', { name: 'Email address' }).press('Tab');
  await page.getByRole('textbox', { name: 'Password', exact: true }).fill('password');

  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle' }), // or 'load' if needed
    page.getByRole('button', { name: 'Log in' }).click()
  ]);
  await page.waitForSelector('text=Dashboard for Task Management Demo App');
  page.once('dialog', async dialog => {
    // console.log(`Dialog message: ${dialog.message()}`);
    expect(dialog.type()).toBe('confirm');
    expect(dialog.message()).toContain('Are you sure');
    await dialog.accept(); // Click "OK"
  });

  await page.getByRole('link', { name: 'Projects' }).click();
  await page.getByRole('button', { name: 'Create' }).click();
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.getByText('The name field is required.').click();
  await page.getByRole('textbox', { name: 'Name' }).click();
  await page.getByRole('textbox', { name: 'Name' }).fill('My Project 4');
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.getByRole('button', { name: 'Edit' }).nth(3).click();
  await page.getByRole('textbox', { name: 'Name' }).click();
  await page.getByRole('textbox', { name: 'Name' }).press('ControlOrMeta+a');
  await page.getByRole('textbox', { name: 'Name' }).press('ControlOrMeta+x');
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.getByText('The name field is required.').click();
  await page.getByRole('textbox', { name: 'Name' }).click();
  await page.getByRole('textbox', { name: 'Name' }).fill('My Project 4 updated');
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.getByText('My Project 4 updated').click();

  await page.getByRole('button', { name: 'Delete' }).nth(3).click();
  // make sure the newly created project got deleted
  await expect(page.locator('text=My Project 4 updated')).toHaveCount(0)
});
