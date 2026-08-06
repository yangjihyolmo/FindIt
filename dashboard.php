<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | FindIt</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary: #1769aa;
            --primary-dark: #0f4f86;
            --primary-light: #eaf4fc;
            --page: #f5f8fb;
            --surface: #ffffff;
            --text: #172033;
            --muted: #667085;
            --border: #e4eaf0;
            --shadow: 0 12px 35px rgba(23, 105, 170, 0.09);
            --radius: 18px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .dashboard-container {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
        }

        /* Simple navbar */
        .dashboard-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .dashboard-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;
            min-height: 72px;
            padding: 0 20px;
            margin: 0 auto;
        }

        .dashboard-logo {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            text-decoration: none;
        }

        .dashboard-menu {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .dashboard-menu a {
            padding: 0;
            color: #1f2937;
            background: transparent;
            border-radius: 0;
            font-size: 16px;
            font-weight: 400;
            text-decoration: none;
            transition:
                color 0.2s,
                background 0.2s;
        }

        .dashboard-menu a:hover {
            color: #1769aa;

        }

        .dashboard-menu a.active {
            color: #1769aa;
            background: transparent;
            font-weight: bold;
        }

        /* Hero */
        .dashboard-hero {
            padding: 54px 0 96px;
            background:
                radial-gradient(circle at 88% 20%, rgba(23, 105, 170, 0.16), transparent 25%),
                linear-gradient(135deg, #edf7ff 0%, #ffffff 58%, #f5f9fc 100%);
            border-bottom: 1px solid var(--border);
        }

        .hero-layout {
            display: grid;
            grid-template-columns: 1.35fr 0.65fr;
            align-items: center;
            gap: 60px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 28px;
            height: 3px;
            border-radius: 20px;
            background: var(--primary);
        }

        .hero-copy h1 {
            margin: 0;
            max-width: 720px;
            font-size: clamp(38px, 5vw, 58px);
            line-height: 1.08;
            letter-spacing: -2px;
        }

        .hero-copy h1 span {
            color: var(--primary);
        }

        .hero-copy>p {
            max-width: 650px;
            margin: 20px 0 0;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.75;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .primary-button,
        .secondary-button {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 22px;
            border-radius: 11px;
            font-size: 15px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .primary-button {
            color: #fff;
            background: var(--primary);
            box-shadow: 0 10px 22px rgba(23, 105, 170, 0.2);
        }

        .primary-button:hover {
            transform: translateY(-2px);
            background: var(--primary-dark);
        }

        .secondary-button {
            color: var(--primary);
            background: #fff;
            border: 1px solid #bdd7ea;
        }

        .secondary-button:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .hero-visual {
            display: flex;
            justify-content: center;
        }

        .profile-orbit {
            width: 220px;
            height: 220px;
            position: relative;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(23, 105, 170, 0.18);
            box-shadow: var(--shadow);
        }

        .profile-orbit::before,
        .profile-orbit::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            border: 1px dashed rgba(23, 105, 170, 0.24);
        }

        .profile-orbit::before {
            inset: -17px;
        }

        .profile-orbit::after {
            inset: 27px;
        }

        .profile-icon {
            width: 112px;
            height: 112px;
            display: grid;
            place-items: center;
            position: relative;
            z-index: 2;
            border-radius: 50%;
            color: #fff;
            background: var(--primary);
            font-size: 50px;
            box-shadow: 0 16px 30px rgba(23, 105, 170, 0.25);
        }

        /* Stats */
        .stats-wrap {
            position: relative;
            z-index: 10;
            margin-top: -48px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .stat-card {
            min-height: 142px;
            display: flex;
            align-items: center;
            gap: 17px;
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 24px;
        }

        .stat-card strong {
            display: block;
            color: var(--text);
            font-size: 32px;
            line-height: 1;
        }

        .stat-card span {
            display: block;
            margin-top: 8px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        /* Sections */
        .dashboard-section {
            padding: 74px 0;
        }

        .dashboard-section.white-section {
            background: #fff;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .section-heading {
            max-width: 650px;
            margin-bottom: 30px;
        }

        .section-heading.centered {
            margin-right: auto;
            margin-left: auto;
            text-align: center;
        }

        .section-heading h2,
        .section-top h2 {
            margin: 0;
            font-size: clamp(28px, 4vw, 38px);
            letter-spacing: -1px;
        }

        .section-heading p {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.7;
        }

        /* Actions */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .action-card {
            display: flex;
            flex-direction: column;
            min-height: 285px;
            padding: 29px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 9px 28px rgba(16, 24, 40, 0.06);
            transition: 0.22s ease;
        }

        .action-card:hover {
            transform: translateY(-6px);
            border-color: #b9d7ec;
            box-shadow: var(--shadow);
        }

        .action-card-icon {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            margin-bottom: 22px;
            border-radius: 16px;
            color: var(--primary);
            background: var(--primary-light);
            font-size: 27px;
        }

        .action-card h3 {
            margin: 0;
            font-size: 21px;
        }

        .action-card p {
            flex: 1;
            margin: 12px 0 24px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .action-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 800;
        }

        .action-link:hover {
            gap: 12px;
        }

        /* Reports */
        .section-top {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 25px;
            margin-bottom: 30px;
        }

        .view-all-link {
            color: var(--primary);
            font-size: 15px;
            font-weight: 800;
            white-space: nowrap;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .report-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 9px 28px rgba(16, 24, 40, 0.06);
            transition: 0.22s ease;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .report-image-wrap {
            height: 205px;
            overflow: hidden;
            position: relative;
            background: #edf4f8;
        }

        .report-image {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            transition: transform 0.35s ease;
        }

        .report-card:hover .report-image {
            transform: scale(1.04);
        }

        .report-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: #80a6c2;
            font-size: 58px;
            background: linear-gradient(135deg, #e7f2f9, #f8fbfd);
        }

        .type-badge {
            position: absolute;
            left: 16px;
            top: 16px;
            padding: 7px 11px;
            border-radius: 999px;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }

        .type-lost {
            background: #d92d20;
        }

        .type-found {
            background: #039855;
        }

        .report-body {
            padding: 23px;
        }

        .report-date {
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .report-body h3 {
            margin: 10px 0 12px;
            font-size: 20px;
            line-height: 1.35;
        }

        .report-location {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .report-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .status-open {
            color: #175cd3;
            background: #eff8ff;
        }

        .status-warning {
            color: #b54708;
            background: #fffaeb;
        }

        .status-success {
            color: #027a48;
            background: #ecfdf3;
        }

        .details-link {
            color: var(--primary);
            font-size: 13px;
            font-weight: 800;
        }

        .empty-state {
            padding: 54px 25px;
            text-align: center;
            background: #fff;
            border: 1px dashed #b7cad8;
            border-radius: var(--radius);
        }

        .empty-state-icon {
            width: 70px;
            height: 70px;
            display: grid;
            place-items: center;
            margin: 0 auto 18px;
            border-radius: 50%;
            color: var(--primary);
            background: var(--primary-light);
            font-size: 31px;
        }

        .empty-state h3 {
            margin: 0;
            font-size: 23px;
        }

        .empty-state p {
            margin: 10px 0 22px;
            color: var(--muted);
        }

        /* Footer */
        .dashboard-footer {
            padding-top: 55px;
            color: #d0d5dd;
            background: #101828;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 0.75fr 0.75fr;
            gap: 55px;
            padding-bottom: 42px;
        }

        .footer-brand h2 {
            margin: 0 0 14px;
            color: #fff;
            font-size: 26px;
        }

        .footer-brand h2 span {
            color: #63b3ed;
        }

        .footer-brand p {
            max-width: 470px;
            margin: 0;
            color: #98a2b3;
            line-height: 1.7;
        }

        .footer-column h3 {
            margin: 0 0 17px;
            color: #fff;
            font-size: 15px;
        }

        .footer-column a {
            display: block;
            width: fit-content;
            margin: 10px 0;
            color: #98a2b3;
            font-size: 14px;
        }

        .footer-column a:hover {
            color: #fff;
        }

        .footer-copy {
            padding: 20px 0;
            text-align: center;
            border-top: 1px solid #344054;
            color: #98a2b3;
            font-size: 13px;
        }

        @media (max-width: 980px) {
            .dashboard-nav {
                flex-direction: column;
                justify-content: center;
                gap: 14px;
                padding-top: 16px;
                padding-bottom: 16px;
            }

            .dashboard-menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 14px 18px;
            }

            .hero-layout {
                grid-template-columns: 1fr;
            }

            .hero-visual {
                display: none;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .action-grid,
            .reports-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-brand {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 680px) {
            .dashboard-container {
                width: min(100% - 28px, 1180px);
            }

            .dashboard-hero {
                padding: 42px 0 80px;
            }

            .hero-copy h1 {
                letter-spacing: -1.3px;
            }

            .hero-copy>p {
                font-size: 15px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .primary-button,
            .secondary-button {
                width: 100%;
            }

            .stats-grid,
            .action-grid,
            .reports-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                min-height: 110px;
            }

            .dashboard-section {
                padding: 58px 0;
            }

            .section-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .report-image-wrap {
                height: 220px;
            }

            .footer-brand {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>
    <header class="dashboard-header">
        <nav class="dashboard-nav">
            <a href="index.php" class="dashboard-logo">FindIt</a>

            <div class="dashboard-menu">
                <a href="index.php">Home</a>
                <a href="browse.php">Browse Items</a>
                <a href="report.php">Report Item</a>
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="my_reports.php">My Reports</a>
                <a href="contact.php">Contact</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="dashboard-hero">
            <div class="dashboard-container hero-layout">
                <div class="hero-copy">
                    <div class="eyebrow">User dashboard</div>

                    <h1>Welcome back, <span>
                            <?php echo htmlspecialchars($fullName); ?>
                        </span></h1>

                    <p>
                        View your activity, manage your lost and found reports, and quickly check the latest status of
                        every item you have submitted.
                    </p>

                    <div class="hero-buttons">
                        <a href="report.php" class="primary-button">+ Report an Item</a>
                        <a href="browse.php" class="secondary-button">Browse Items</a>
                    </div>
                </div>

                <div class="hero-visual" aria-hidden="true">
                    <div class="profile-orbit">
                        <div class="profile-icon">👤</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-wrap">
            <div class="dashboard-container stats-grid">
                <article class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div>
                        <strong>
                            <?php echo (int) $totalReports; ?>
                        </strong>
                        <span>Total Reports</span>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">🔎</div>
                    <div>
                        <strong>
                            <?php echo (int) $lostItems; ?>
                        </strong>
                        <span>Lost Items</span>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div>
                        <strong>
                            <?php echo (int) $foundItems; ?>
                        </strong>
                        <span>Found Items</span>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">✓</div>
                    <div>
                        <strong>
                            <?php echo (int) $returnedItems; ?>
                        </strong>
                        <span>Returned Items</span>
                    </div>
                </article>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="dashboard-container">
                <div class="section-heading centered">
                    <div class="eyebrow">Quick actions</div>
                    <h2>What would you like to do?</h2>
                    <p>Use the shortcuts below to report, search, or manage your items.</p>
                </div>

                <div class="action-grid">
                    <article class="action-card">
                        <div class="action-card-icon">✎</div>
                        <h3>Report an Item</h3>
                        <p>Submit information about something you lost or an item you found.</p>
                        <a href="report.php" class="action-link">Report now <span>→</span></a>
                    </article>

                    <article class="action-card">
                        <div class="action-card-icon">⌕</div>
                        <h3>Browse Reports</h3>
                        <p>Search through the latest lost and found items reported by the community.</p>
                        <a href="browse.php" class="action-link">Browse items <span>→</span></a>
                    </article>

                    <article class="action-card">
                        <div class="action-card-icon">☷</div>
                        <h3>My Reports</h3>
                        <p>Review, edit, and manage all of the lost and found reports you submitted.</p>
                        <a href="my_reports.php" class="action-link">View reports <span>→</span></a>
                    </article>
                </div>
            </div>
        </section>

        <section class="dashboard-section white-section">
            <div class="dashboard-container">
                <div class="section-top">
                    <div>
                        <div class="eyebrow">Your activity</div>
                        <h2>Recent Reports</h2>
                    </div>

                    <a href="my_reports.php" class="view-all-link">View all reports →</a>
                </div>

                <?php if (!empty($recentItems)): ?>
                <div class="reports-grid">
                    <?php foreach ($recentItems as $item): ?>
                    <?php
                            $itemType = strtolower(trim((string) $item["item_type"]));
                            $typeClass = $itemType === "lost" ? "type-lost" : "type-found";
                            $statusClass = safeStatusClass((string) $item["status"]);
                            ?>

                    <article class="report-card">
                        <div class="report-image-wrap">
                            <?php if (!empty($item["image_name"])): ?>
                            <img src="uploads/<?php echo rawurlencode(basename($item[" image_name"])); ?>"
                            alt="
                            <?php echo htmlspecialchars($item["item_name"]); ?>" class="report-image">
                            <?php else: ?>
                            <div class="report-placeholder">📦</div>
                            <?php endif; ?>

                            <span class="type-badge <?php echo $typeClass; ?>">
                                <?php echo htmlspecialchars($item["item_type"]); ?>
                            </span>
                        </div>

                        <div class="report-body">
                            <span class="report-date">
                                <?php echo htmlspecialchars($item["item_date"]); ?>
                            </span>

                            <h3>
                                <?php echo htmlspecialchars($item["item_name"]); ?>
                            </h3>

                            <p class="report-location">
                                📍
                                <?php echo htmlspecialchars($item["location"]); ?>
                            </p>

                            <div class="report-bottom">
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($item["status"]); ?>
                                </span>

                                <a href="item.php?id=<?php echo (int) $item[" item_id"]; ?>" class="details-link">
                                    View details →
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>No reports yet</h3>
                    <p>You have not submitted a lost or found item report.</p>
                    <a href="report.php" class="primary-button">Create Your First Report</a>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="dashboard-footer">
        <div class="dashboard-container footer-grid">
            <div class="footer-brand">
                <h2>Find<span>It</span></h2>
                <p>A simple lost and found management system that helps people report items and reconnect belongings
                    with their owners.</p>
            </div>

            <div class="footer-column">
                <h3>Pages</h3>
                <a href="browse.php">Browse Items</a>
                <a href="report.php">Report Item</a>
                <a href="contact.php">Contact</a>
            </div>

            <div class="footer-column">
                <h3>Account</h3>
                <a href="dashboard.php">Dashboard</a>
                <a href="my_reports.php">My Reports</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="footer-copy">
            <div class="dashboard-container">© 2026 FindIt Management System</div>
        </div>
    </footer>
</body>

</html>