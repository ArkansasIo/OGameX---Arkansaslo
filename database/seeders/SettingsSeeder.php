<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Seed all default server settings.
     *
     * These settings are used throughout the application and are exposed in
     * the admin server-settings panel. Each entry uses insertOrIgnore so that
     * running the seeder on an existing installation never overwrites values
     * that an admin has already customised.
     *
     * @return void
     */
    public function run(): void
    {
        $now = now();

        $settings = [
            // ── General ──────────────────────────────────────────────────────
            ['key' => 'game_name',                'value' => 'OGameX'],
            ['key' => 'universe_name',            'value' => 'Universe'],
            ['key' => 'number_of_galaxies',       'value' => '9'],
            ['key' => 'battle_engine',            'value' => 'rust'],

            // ── Speed multipliers ─────────────────────────────────────────────
            ['key' => 'fleet_speed_war',          'value' => '1'],
            ['key' => 'fleet_speed_holding',      'value' => '1'],
            ['key' => 'fleet_speed_peaceful',     'value' => '1'],
            ['key' => 'economy_speed',            'value' => '1'],
            ['key' => 'research_speed',           'value' => '1'],

            // ── Basic income (per hour) ───────────────────────────────────────
            ['key' => 'basic_income_metal',       'value' => '30'],
            ['key' => 'basic_income_crystal',     'value' => '15'],
            ['key' => 'basic_income_deuterium',   'value' => '0'],
            ['key' => 'basic_income_energy',      'value' => '0'],

            // ── Registration / new-player defaults ───────────────────────────
            ['key' => 'registration_planet_amount', 'value' => '1'],
            ['key' => 'planet_fields_bonus',      'value' => '0'],
            ['key' => 'dark_matter_bonus',        'value' => '8000'],

            // ── Alliance system ───────────────────────────────────────────────
            ['key' => 'alliance_combat_system_on', 'value' => '1'],
            ['key' => 'alliance_cooldown_days',   'value' => '3'],

            // ── Debris fields ─────────────────────────────────────────────────
            ['key' => 'debris_field_from_ships',  'value' => '30'],
            ['key' => 'debris_field_from_defense', 'value' => '0'],
            ['key' => 'debris_field_deuterium_on', 'value' => '0'],

            // ── Wreck fields ──────────────────────────────────────────────────
            ['key' => 'wreck_field_min_resources_loss',   'value' => '150000'],
            ['key' => 'wreck_field_min_fleet_percentage', 'value' => '5'],
            ['key' => 'wreck_field_lifetime_hours',       'value' => '72'],
            ['key' => 'wreck_field_repair_max_hours',     'value' => '12'],
            ['key' => 'wreck_field_repair_min_minutes',   'value' => '30'],

            // ── Moon formation ────────────────────────────────────────────────
            ['key' => 'maximum_moon_chance',      'value' => '20'],

            // ── Galaxy view ───────────────────────────────────────────────────
            ['key' => 'ignore_empty_systems_on',    'value' => '0'],
            ['key' => 'ignore_inactive_systems_on', 'value' => '0'],

            // ── Battle mechanics ──────────────────────────────────────────────
            ['key' => 'defense_repair_rate',      'value' => '70'],
            ['key' => 'hamill_manoeuvre_chance',  'value' => '1000'],

            // ── Highscores ────────────────────────────────────────────────────
            ['key' => 'highscore_admin_visible',  'value' => '0'],

            // ── Dark matter ───────────────────────────────────────────────────
            ['key' => 'dark_matter_initial',                  'value' => '8000'],
            ['key' => 'dark_matter_regen_enabled',            'value' => '0'],
            ['key' => 'dark_matter_regen_amount',             'value' => '150000'],
            ['key' => 'dark_matter_regen_period',             'value' => '604800'],
            ['key' => 'expedition_dark_matter_multiplier',    'value' => '1.0'],
            ['key' => 'expedition_dark_matter_min_pathfinder',    'value' => '300'],
            ['key' => 'expedition_dark_matter_max_pathfinder',    'value' => '400'],
            ['key' => 'expedition_dark_matter_min_no_pathfinder', 'value' => '150'],
            ['key' => 'expedition_dark_matter_max_no_pathfinder', 'value' => '200'],

            // ── Premium / merchant ────────────────────────────────────────────
            ['key' => 'commanding_staff_cost_per_week', 'value' => '42500'],
            ['key' => 'player_class_change_cost',       'value' => '500000'],
            ['key' => 'merchant_cost_per_use',          'value' => '3500'],

            // ── Planet relocation ─────────────────────────────────────────────
            ['key' => 'planet_relocation_cost',     'value' => '240000'],
            ['key' => 'planet_relocation_duration', 'value' => '86400'],

            // ── Expedition rewards ────────────────────────────────────────────
            ['key' => 'bonus_expedition_slots',                    'value' => '0'],
            ['key' => 'expedition_reward_multiplier_resources',    'value' => '1.0'],
            ['key' => 'expedition_reward_multiplier_ships',        'value' => '1.0'],
            ['key' => 'expedition_reward_multiplier_dark_matter',  'value' => '1.0'],
            ['key' => 'expedition_reward_multiplier_items',        'value' => '1.0'],

            // ── Expedition outcome weights ────────────────────────────────────
            ['key' => 'expedition_weight_ships',       'value' => '17.9'],
            ['key' => 'expedition_weight_resources',   'value' => '36.8'],
            ['key' => 'expedition_weight_delay',       'value' => '7.9'],
            ['key' => 'expedition_weight_speedup',     'value' => '2.9'],
            ['key' => 'expedition_weight_nothing',     'value' => '26.3'],
            ['key' => 'expedition_weight_black_hole',  'value' => '0.2'],
            ['key' => 'expedition_weight_pirates',     'value' => '3.0'],
            ['key' => 'expedition_weight_aliens',      'value' => '1.5'],
            ['key' => 'expedition_weight_dark_matter', 'value' => '7.9'],
            ['key' => 'expedition_weight_merchant',    'value' => '0.4'],
            ['key' => 'expedition_weight_items',       'value' => '0'],

            // ── Legal / content pages (empty by default) ──────────────────────
            ['key' => 'rules_content',          'value' => ''],
            ['key' => 'legal_content',          'value' => ''],
            ['key' => 'privacy_policy_content', 'value' => ''],
            ['key' => 'terms_content',          'value' => ''],
            ['key' => 'contact_content',        'value' => ''],
        ];

        foreach ($settings as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        // insertOrIgnore keeps existing admin customisations intact.
        DB::table('settings')->insertOrIgnore($settings);
    }
}
