<template lang="pug">
    .container
        h1 Доски
        form(@submit.prevent="addNewDesk")
            .form-group
                input.form-control(type="text" placeholder="Введите название доски" v-model="name" :class="{ 'is-invalid': $v.name.$error }")
                .invalid-feedback(v-if="!$v.name.required") Обязательное поле.
                .invalid-feedback(v-if="!$v.name.maxLength") Макс. количество символов: {{ $v.name.$params.maxLength.max }}.
            button.btn.btn-primary.mt-2(type="submit") Добавить
        .alert.alert-danger.mt-3(role="alert" v-if="errored").
            Ошибка загрузки данных <br>
            {{ errors[0] }}
        .row
            .col-lg-4(v-for="desk in desks")
                .card.mt-3
                    router-link.card-body(:to="{ name: 'showDesk', params: {deskId: desk.id}}")
                        h4.card-title {{ desk.name }}
                    button.btn.btn-danger.mt-3(type="button" @click="deleteDesk(desk.id)") Удалить
        .text-center(v-if="loading")
            .spinner-border(role="status")
                span.visually-hidden Loading...
</template>

<script>
    import { required, maxLength } from 'vuelidate/lib/validators'

    export default {
        data(){
            return {
               desks: [],
               errored: false,
               errors: [],
               loading: true,
               name: null
            }
        },
        mounted(){
            this.getAllDesks()
        },
        methods: {
            getAllDesks(){
                //Подключается вместе с bootstrap
                axios.get('/api/V1/desks')
                    .then(response => {
                        this.desks = response.data.data
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    }).finally(() => {
                        this.loading = false
                    })
            },
            deleteDesk(id){
                if(confirm('Вы действительно хотите удалить диску?')){
                    axios.post('/api/V1/desks/' + id, {
                        _method: 'DELETE',
                    })
                    .then(response => {
                        this.desks = []
                        this.getAllDesks()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    }).finally(() => {
                        this.loading = false
                    })
                }
            },
            addNewDesk(){
                this.errored = false
                this.$v.$touch() //вызов проверки валидации
                if(this.$v.$anyError){
                    return;
                }
                axios.post('/api/V1/desks', {
                        name: this.name
                    })
                    .then(response => {
                        this.$v.$reset()
                        this.name = ''
                        this.desks = []
                        this.getAllDesks()
                    })
                    .catch(error => {
                        console.log(error)
                        if(error.response.data.errors.name){
                            this.errors = []
                            this.errors.push(error.response.data.errors.name[0])
                        }
                        this.errored = true
                    }).finally(() => {
                        this.loading = false
                    })
            }
        },
        validations: {
            name: {
                required,
                maxLength: maxLength(255)
            }
        }
    }
</script>