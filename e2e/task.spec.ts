import { test, expect } from '@playwright/test';

test('Task can be created, edited and deleted', async ({ page }) => {
  await page.goto('http://localhost:8000/login');
  await page.getByRole('textbox', { name: 'Email address' }).click();
  await page.getByRole('textbox', { name: 'Email address' }).fill('tbattur22@yahoo.com');
  await page.getByRole('textbox', { name: 'Email address' }).press('Tab');
  await page.getByRole('textbox', { name: 'Password' }).fill('password');
  await page.getByRole('button', { name: 'Log in' }).click();
  await page.waitForSelector('text=Dashboard for Task Management Demo App');

  await page.getByRole('link', { name: 'Task Management' }).click();
  await page.getByRole('combobox').selectOption('3');
  await page.goto('http://localhost:8000/tasks/select_project/3');
  await page.getByRole('button', { name: 'Add Task' }).click();
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.getByText('The name field is required.').click();
  await page.getByText('The priority has already been').click();
  await page.getByRole('textbox', { name: 'Task name' }).click();
  await page.getByRole('textbox', { name: 'Task name' }).fill('Test Task 2 for Project 3');
  await page.getByRole('textbox', { name: 'Task priority' }).click();
  await page.getByRole('textbox', { name: 'Task priority' }).fill('2');
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.goto('http://localhost:8000/');
  await page.getByRole('heading', { name: 'Test Task 2 for Project' }).click();
  await page.getByRole('button', { name: 'Edit' }).nth(1).click();
  await page.getByRole('textbox', { name: 'Task name' }).click();
  await page.getByRole('textbox', { name: 'Task name' }).press('ControlOrMeta+a');
  await page.getByRole('textbox', { name: 'Task name' }).press('ControlOrMeta+x');
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.getByText('The name field is required.').click();
  await page.getByRole('textbox', { name: 'Task name' }).click();
  await page.getByRole('textbox', { name: 'Task name' }).fill('Test Task 2 for Project 3 updated');
  await page.getByRole('button', { name: 'Submit' }).click();
  await page.goto('http://localhost:8000/');
  await page.getByRole('heading', { name: 'Test Task 2 for Project 3' }).click();
  page.once('dialog', dialog => {
    console.log(`Dialog message: ${dialog.message()}`);
    dialog.accept();
  });
  await page.getByRole('button', { name: 'Delete' }).nth(1).click();
  // make sure the newly created project got deleted
  await expect(page.locator('text=Test Task 2 for Project 3 updated')).toHaveCount(0)

  await page.getByRole('heading', { name: 'Task 1' }).click();
  await page.getByText('Project Id:').click();
  await page.getByText('Priority:').click();
});
