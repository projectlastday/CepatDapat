<?php

return [
    'super_admin_role' => 7,

    'editable_roles' => [1, 2, 3, 5, 6],

    'roles_labels' => [
        1 => 'Admin',
        2 => 'Moderator',
        3 => 'Member',
        5 => 'Manager',
        6 => 'Super Moderator',
        7 => 'Super Admin',
    ],

    'features' => [
        // Dashboard
        'dashboard_view' => [
            'label' => 'Lihat Dashboard',
            'group' => 'Dashboard',
        ],

        // Laporan
        'report_view' => [
            'label' => 'Lihat Laporan',
            'group' => 'Laporan',
        ],

        // History
        'history_view' => [
            'label' => 'Lihat Log Riwayat',
            'group' => 'Riwayat',
        ],

        // Settings
        'setting_view' => [
            'label' => 'Lihat Pengaturan',
            'group' => 'Pengaturan',
        ],
        'hak_akses_view' => [
            'label' => 'Kelola Hak Akses',
            'group' => 'Pengaturan',
        ],

        // Auction Management
        'auction_create' => [
            'label' => 'Buat/Mulai Lelang',
            'group' => 'Manajemen Lelang',
        ],
        'auction_bid' => [
            'label' => 'Tawar Lelang',
            'group' => 'Manajemen Lelang',
        ],
        'auction_manage_view' => [
            'label' => 'Lihat Manajemen Lelang',
            'group' => 'Manajemen Lelang',
        ],
        'auction_cancel' => [
            'label' => 'Batalkan Lelang',
            'group' => 'Manajemen Lelang',
        ],
        'auction_delete' => [
            'label' => 'Hapus Lelang',
            'group' => 'Manajemen Lelang',
        ],

        // Canceled Auctions
        'auction_canceled_view' => [
            'label' => 'Lihat Lelang Dibatalkan',
            'group' => 'Dibatalkan/Dihapus',
        ],
        'auction_uncancel' => [
            'label' => 'Kembalikan Lelang Dibatalkan',
            'group' => 'Dibatalkan/Dihapus',
        ],

        // Deleted Auctions
        'auction_deleted_view' => [
            'label' => 'Lihat Lelang Dihapus',
            'group' => 'Dibatalkan/Dihapus',
        ],
        'auction_restore' => [
            'label' => 'Pulihkan Lelang',
            'group' => 'Dibatalkan/Dihapus',
        ],

        // Moderation
        'moderation_view' => [
            'label' => 'Lihat Moderasi',
            'group' => 'Moderasi',
        ],
        'moderation_action' => [
            'label' => 'Verifikasi/Tolak Pengguna',
            'group' => 'Moderasi',
        ],
        'user_suspend' => [
            'label' => 'Suspend Pengguna',
            'group' => 'Moderasi',
        ],
    ],

    // Default permission matrix (Role ID => [Feature Keys])
    'default_matrix' => [
        // 1: Admin
        1 => [
            'dashboard_view',
            'report_view',
            // 'history_view', // Admin (1) originally did NOT have history access (only 7). Removed to match.
            'auction_create',
            'auction_bid',
            'auction_manage_view',
            'auction_cancel',
            'auction_delete',
            'moderation_view',
            'moderation_action',
            'user_suspend',
        ],

        // 2: Petugas
        2 => [
            'dashboard_view', // Petugas not in DashboardController line 14 check ([1, 5, 7]), but usually officers have some access. Stick to code: NO dashboard.
            'auction_create', // Petugas (2) in [1, 2] for store
            'auction_bid',    // Petugas (2) in [1, 2] for bid
        ],

        // 3: Masyarakat
        3 => [
            // Minimal access, mostly public frontend
            'auction_bid', // Actually code limits bid to [1, 2], so default masyarakat can't bid? Wait.
            // AuctionController line 202: if (!in_array(session('id_user_type'), [1, 2])) -> redirect back.
            // So roles 3, 5, 6, 7 cannot place bids??
            // I will replicate EXACTLY what the code says.
        ],

        // 5: Eksekutif
        5 => [
            'dashboard_view', // [1, 5, 7]
            'report_view',    // [1, 5, 7]
        ],

        // 6: Super Moderator
        6 => [
            'auction_cancel', // [1, 6, 7] in cancel_auction
        ],

        // 7: Super Admin (Implicitly ALL, but defined here for completeness if logic changes)
        7 => [],
    ],
];
