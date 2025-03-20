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

        -- Contact Table
        CREATE TABLE IF NOT EXISTS `namingo_contact` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `identifier` varchar(255) NOT NULL,
            `voice` varchar(17) default NULL,
            `fax` varchar(17) default NULL,
            `email` varchar(255) NOT NULL,
            `name` varchar(255) NOT NULL,
            `org` varchar(255) default NULL,
            `street1` varchar(255) default NULL,
            `street2` varchar(255) default NULL,
            `street3` varchar(255) default NULL,
            `city` varchar(255) NOT NULL,
            `sp` varchar(255) default NULL,
            `pc` varchar(16) default NULL,
            `cc` char(2) NOT NULL,
            `clid` int(10) unsigned NOT NULL,
            `crdate` datetime(3) NOT NULL,
            `upid` int(10) unsigned default NULL,
            `lastupdate` datetime(3) default NULL,
            `disclose_voice` enum('0','1') NOT NULL default '1',
            `disclose_fax` enum('0','1') NOT NULL default '1',
            `disclose_email` enum('0','1') NOT NULL default '1',
            `disclose_name_int` enum('0','1') NOT NULL default '1',
            `disclose_org_int` enum('0','1') NOT NULL default '1',
            `disclose_addr_int` enum('0','1') NOT NULL default '1',
            `nin` varchar(255) default NULL,
            `nin_type` enum('personal','business') default NULL,
            `validation` enum('0','1','2','3','4'),
            `validation_stamp` datetime(3) default NULL,
            `validation_log` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `identifier` (`identifier`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Domain Table
        CREATE TABLE IF NOT EXISTS `namingo_domain` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(68) NOT NULL,
            `registry_domain_id` varchar(68) NOT NULL,
            `registrant` int(10) unsigned default NULL,
            `admin` int(10) unsigned default NULL,
            `tech` int(10) unsigned default NULL,
            `billing` int(10) unsigned default NULL,
            `ns1` varchar(68) default NULL,
            `ns2` varchar(68) default NULL,
            `ns3` varchar(68) default NULL,
            `ns4` varchar(68) default NULL,
            `ns5` varchar(68) default NULL,
            `crdate` datetime(3) NOT NULL,
            `exdate` datetime(3) NOT NULL,
            `lastupdate` datetime(3) default NULL,
            `clid` int(10) unsigned NOT NULL,
            `crid` int(10) unsigned NOT NULL,
            `upid` int(10) unsigned default NULL,
            `trdate` datetime(3) default NULL,
            `trstatus` enum('clientApproved','clientCancelled','clientRejected','pending','serverApproved','serverCancelled') default NULL,
            `reid` int(10) unsigned default NULL,
            `redate` datetime(3) default NULL,
            `acid` int(10) unsigned default NULL,
            `acdate` datetime(3) default NULL,
            `transfer_exdate` datetime(3) default NULL,
            `idnlang` varchar(16) default NULL,
            `delTime` datetime(3) default NULL,
            `resTime` datetime(3) default NULL,
            `rgpstatus` enum('addPeriod','autoRenewPeriod','renewPeriod','transferPeriod','pendingDelete','pendingRestore','redemptionPeriod') default NULL,
            `rgppostData` text default NULL,
            `rgpdelTime` datetime(3) default NULL,
            `rgpresTime` datetime(3) default NULL,
            `addPeriod` tinyint(3) unsigned default NULL,
            `autoRenewPeriod` tinyint(3) unsigned default NULL,
            `renewPeriod` tinyint(3) unsigned default NULL,
            `transferPeriod` tinyint(3) unsigned default NULL,
            `renewedDate` datetime(3) default NULL,
            `agp_exempted` tinyint(1) DEFAULT 0,
            `agp_request` datetime(3) default NULL,
            `agp_grant` datetime(3) default NULL,
            `agp_reason` text default NULL,
            `agp_status` varchar(30) default NULL,
            `tm_notice_accepted` datetime(3) default NULL,
            `tm_notice_expires` datetime(3) default NULL,
            `tm_notice_id` varchar(150) default NULL,
            `tm_notice_validator` varchar(30) default NULL,
            `tm_smd_id` text default NULL,
            `tm_phase` VARCHAR(100) NOT NULL DEFAULT 'NONE',
            `phase_name` VARCHAR(75) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Domain Status Table
        CREATE TABLE IF NOT EXISTS `namingo_domain_status` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `domain_id` int(10) NOT NULL,
            `status` varchar(100) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `domain_status_unique` (`domain_id`, `status`),
            CONSTRAINT `domain_status_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `tbldomains`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- DNSSEC Table
        CREATE TABLE IF NOT EXISTS `namingo_domain_dnssec` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
        Capsule::schema()->dropIfExists('namingo_contact');
        Capsule::schema()->dropIfExists('namingo_domain');
        Capsule::schema()->dropIfExists('namingo_domain_dnssec');
        Capsule::schema()->dropIfExists('namingo_domain_status');
        
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
