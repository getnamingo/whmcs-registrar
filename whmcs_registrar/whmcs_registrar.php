<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Module configuration.
 */
function whmcs_registrar_config() {
    return [
        'name' => 'ICANN Registrar Accreditation',
        'description' => 'Module for Implementing ICANN Accreditation Database Structure',
        'author' => 'Namingo',
        'version' => '1.0',
    ];
}

/**
 * Code to perform when the module is activated.
 */
function whmcs_registrar_activate() {
    try {
        $sql = "
        -- Domain Meta Table
        CREATE TABLE IF NOT EXISTS `domain_meta` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `domain_id` int(10) NOT NULL,
            `registry_domain_id` varchar(100) DEFAULT NULL,
            `reseller` varchar(255) DEFAULT NULL,
            `reseller_url` varchar(255) DEFAULT NULL,
            `registrant_contact_id` varchar(100) DEFAULT NULL,
            `admin_contact_id` varchar(100) DEFAULT NULL,
            `tech_contact_id` varchar(100) DEFAULT NULL,
            `billing_contact_id` varchar(100) DEFAULT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `domain_id` (`domain_id`),
            CONSTRAINT `domain_meta_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `tbldomains`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        -- Domain Status Table
        CREATE TABLE IF NOT EXISTS `domain_status` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `domain_id` int(10) NOT NULL,
            `status` varchar(100) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `domain_status_unique` (`domain_id`, `status`),
            CONSTRAINT `domain_status_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `tbldomains`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        -- DNSSEC Table
        CREATE TABLE IF NOT EXISTS `domain_dnssec` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `domain_id` int(10) NOT NULL,
            `key_tag` int(11) NOT NULL,
            `algorithm` varchar(10) NOT NULL,
            `digest_type` varchar(10) NOT NULL,
            `digest` text NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `key_tag` (`key_tag`),
            CONSTRAINT `domain_dnssec_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `tbldomains`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        Capsule::unprepared($sql);

        return [
            'status' => 'success',
            'description' => 'Module activated successfully.',
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Activation failed: ' . $e->getMessage(),
        ];
    }
}

/**
 * Code to perform when the module is deactivated.
 */
function whmcs_registrar_deactivate() {
    try {
        Capsule::schema()->dropIfExists('domain_dnssec');
        Capsule::schema()->dropIfExists('domain_status');
        Capsule::schema()->dropIfExists('domain_meta');

        return [
            'status' => 'success',
            'description' => 'Module deactivated successfully.',
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Deactivation failed: ' . $e->getMessage(),
        ];
    }
}

/**
 * Admin area output for the module.
 */
function whmcs_registrar_output($vars) {
    echo '<p>ICANN Registrar Accreditation Module is currently active.</p>';
}
