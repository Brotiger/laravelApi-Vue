<template lang="pug">
    #app
        nav.navbar.navbar-expand-lg.navbar-light.bg-light
            .container
                button.navbar-toggler(type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation")
                    span.navbar-toggler-icon
                #navbarNav.collapse.navbar-collapse
                    ul.navbar-nav
                        li.nav-item
                            router-link.nav-link(:to="{name: 'home'}") Главная
                        li.nav-item
                            router-link.nav-link(:to="{name: 'desks'}") Доски
        .container
            router-view
</template>

<script>
    export default {
        
    }
</script>