<?php

use App\Models\BusinessSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Clean up all business settings before each test
    BusinessSetting::query()->delete();
});

test('can set and get a string value', function () {
    BusinessSetting::set('app_name', 'CLIENTBRIDGE', 'string');

    $value = BusinessSetting::get('app_name');

    expect($value)->toBe('CLIENTBRIDGE');
});

test('can set and get an integer value', function () {
    BusinessSetting::set('max_clients', 100, 'integer');

    $value = BusinessSetting::get('max_clients');

    expect($value)->toBe(100);
});

test('can set and get a boolean value', function () {
    BusinessSetting::set('maintenance_mode', true, 'boolean');

    $value = BusinessSetting::get('maintenance_mode');

    expect($value)->toBeTrue();
});

test('can set and get a float value', function () {
    BusinessSetting::set('tax_rate', 7.5, 'float');

    $value = BusinessSetting::get('tax_rate');

    expect($value)->toBe(7.5);
});

test('can set and get a json value', function () {
    $data = ['key1' => 'value1', 'key2' => 'value2'];

    BusinessSetting::set('config_data', $data, 'json');

    $value = BusinessSetting::get('config_data');

    expect($value)->toBe($data);
});

test('returns default value when key does not exist', function () {
    $value = BusinessSetting::get('nonexistent_key', 'default_value');

    expect($value)->toBe('default_value');
});

test('can update existing setting', function () {
    BusinessSetting::set('app_name', 'OLD_NAME', 'string');
    BusinessSetting::set('app_name', 'NEW_NAME', 'string');

    $value = BusinessSetting::get('app_name');

    expect($value)->toBe('NEW_NAME');
});

test('does not create duplicate keys', function () {
    BusinessSetting::set('app_name', 'FIRST', 'string');
    BusinessSetting::set('app_name', 'SECOND', 'string');

    $count = BusinessSetting::where('key', 'app_name')->count();

    expect($count)->toBe(1);
});

test('can get all settings', function () {
    BusinessSetting::set('app_name', 'CLIENTBRIDGE', 'string');
    BusinessSetting::set('max_clients', 100, 'integer');
    BusinessSetting::set('maintenance_mode', true, 'boolean');

    $all = BusinessSetting::getAll();

    expect($all)->toHaveKeys(['app_name', 'max_clients', 'maintenance_mode']);
    expect($all['app_name'])->toBe('CLIENTBRIDGE');
    expect($all['max_clients'])->toBe(100);
    expect($all['maintenance_mode'])->toBeTrue();
});

test('handles special characters in string values', function () {
    BusinessSetting::set('special_chars', 'Test & "quotes" \'apostrophe\' <html>', 'string');

    $value = BusinessSetting::get('special_chars');

    expect($value)->toBe('Test & "quotes" \'apostrophe\' <html>');
});

test('handles empty string values', function () {
    BusinessSetting::set('empty_value', '', 'string');

    $value = BusinessSetting::get('empty_value');

    expect($value)->toBe('');
});

test('handles null values', function () {
    BusinessSetting::set('null_value', null, 'string');

    $value = BusinessSetting::get('null_value');

    expect($value)->toBe('');
});
