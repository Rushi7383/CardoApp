<?php
// Include the header, which has the session check and navigation
require_once 'includes/header.php';

// At this point, we know the admin is logged in.
?>

<div class="welcome-message">
    <h2>Dashboard</h2>
    <p>From here you can manage all aspects of the Cardo application.</p>
</div>

<style>
.stats-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.5rem;
}
.card {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-align: center;
    min-width: 200px;
    flex-grow: 1;
}
.card h3 {
    margin-top: 0;
    color: #495057;
}
.card p {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
    margin: 0;
}
</style>

<div class="stats-cards">
    <div class="card">
        <h3>Total Users</h3>
        <p>0</p> <!-- This would be dynamic later -->
    </div>
    <div class="card">
        <h3>Total Orders</h3>
        <p>0</p> <!-- This would be dynamic later -->
    </div>
    <div class="card">
        <h3>Pending Payments</h3>
        <p>0</p> <!-- This would be dynamic later -->
    </div>
    <div class="card">
        <h3>Open Queries</h3>
        <p>0</p> <!-- This would be dynamic later -->
    </div>
</div>

<?php
// Include the footer
require_once 'includes/footer.php';
?>
