<?php

use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->use(Illuminate\Foundation\Testing\DatabaseMigrations::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function assertMyModels(array $functions)
{
    foreach ($functions as $function) {
        $page = assertMyModel(...$function());
    }
    return $page;
}
function assertMyModel($page, $property, $component, $expectedValues, $fields)
{
    $page->component($component);

    if ($expectedValues instanceof Collection) {
        $page->has($property, $expectedValues->count());

        // check each project values (id and name)
        foreach ($expectedValues as $key => $expectedValue) {
            assertMyPropsAtKey($page, $property, $fields, $key, $expectedValue);
        }
    } else {
        assertMyProps($page, $property, $fields, $expectedValues);
    }
    return $page;
}
function assertMyPropsAtKey($page, $property, $fields, $key, $expectedValue)
{
    foreach ($fields as $field) {
        $page->where("{$property}.{$key}.{$field}", $expectedValue->{$field});
    }
}
function assertMyProps($page, $property, $fields, $expectedValue)
{
    foreach ($fields as $field) {
        $page->where("{$property}.{$field}", $expectedValue->{$field});
    }
}
function something()
{
    // ..
}
function login($user=null)
{
    return test()->actingAs($user ?? User::factory()->create());
}
