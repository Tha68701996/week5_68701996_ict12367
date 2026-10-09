<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/">Loyal MONKEY</router-link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>

          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Customer
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/customer">Show Customers</a></li>
            <li><a class="dropdown-item" href="/customer_crud">Customer CRUD</a></li>
          </ul>
          </li>

          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Employee
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/employee">Show Employees</a></li>
            <li><a class="dropdown-item" href="/employee_crud">Employee CRUD</a></li>
          </ul>
          </li>

          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Contact
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/contact">Show Contacts</a></li>
            <li><a class="dropdown-item" href="/contact_crud">Contact CRUD</a></li>
          </ul>
          </li>

           <li class="nav-item">
            <router-link class="nav-link" to="/about">About</router-link>
          </li>

        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "Navbar"
}
</script>


