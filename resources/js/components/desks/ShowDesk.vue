<template lang="pug">
    .container
        h1 {{ name }}
        .form-group
            input.form-control(type="text" @blur="saveName" v-model="name" :class="{ 'is-invalid': $v.name.$error }")
            .invalid-feedback(v-if="!$v.name.required") Обязательное поле.
            .invalid-feedback(v-if="!$v.name.maxLength") Макс. количество символов: {{ $v.name.$params.maxLength.max }}.
        form(@submit.prevent="addNewDeskList()")
            .form-group
                input.form-control.mt-2(type="text" placeholder="Введите название списка" v-model="desk_list_name" :class="{ 'is-invalid': $v.desk_list_name.$error }")
                .invalid-feedback(v-if="!$v.desk_list_name.required") Обязательное поле.
                .invalid-feedback(v-if="!$v.desk_list_name.maxLength") Макс. количество символов: {{ $v.desk_list_name.$params.maxLength.max }}.
            button.btn.btn-primary.mt-2(type="submit") Добавить
        .alert.alert-danger(role="alert" v-if="errored") Ошибка загрузки данных
        .row
            .col-lg-4(v-for="(desk_list, index) in desk_lists")
                .card.mt-3
                    .card-body
                        form.d-flex.justify-content-between.align-items-center(@submit.prevent="updateDeskList(desk_list.id, desk_list.name)" v-if="desk_list_input_id == desk_list.id")
                            input.form-control(type="text" placeholder="Введите название списка" v-model="desk_list.name" :class="{ 'is-invalid': $v.desk_lists.$each[index].name.$error }")
                            button.btn.btn-danger(type="button" @click="updateDeskList(desk_list.id, desk_list.name)")
                                i.fas.fa-times
                            .invalid-feedback(v-if="!$v.desk_lists.$each[index].name.required") Обязательное поле.
                            .invalid-feedback(v-if="!$v.desk_lists.$each[index].name.maxLength") Макс. количество символов: {{ $v.desk_lists.$each[index].name.$params.maxLength.max }}.
                        h4.card-titled.d-flex.justify-content-between.align-items-center(v-else style="cursor: pointer" @click="desk_list_input_id = desk_list.id") {{ desk_list.name }}
                            i.fas.fa-pencil-alt
                        .card.mt-3(v-for="card in desk_list.cards" :key="card.id")
                            .card-body
                                h5.card-title {{ card.name }}
                                button.btn.btn-primary.me-2(@click="getCard(card.id)" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal") Открыть
                                button.btn.btn-danger(@click="deleteCard(card.id)") Удалить
                        form.mt-3(@submit.prevent="addNewCard(desk_list.id)")
                            input.form-control(type="text" v-model="card_names[desk_list.id]" placeholder="Введите название карточки" :class="{ 'is-invalid': $v.card_names.$each[desk_list.id].$error }")
                            .invalid-feedback(v-if="!$v.card_names.$each[desk_list.id].required") Обязательное поле.
                            .invalid-feedback(v-if="!$v.card_names.$each[desk_list.id].maxLength") Макс. количество символов: {{ $v.card_names.$each[desk_list.id].$params.maxLength.max }}.
                        .modal.fade#exampleModal(tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true")
                            .modal-dialog.modal-lg
                                .modal-content
                                    .modal-header
                                        form.d-flex.justify-content-between.align-items-center(@submit.prevent="updateCardName" v-if="show_card_name_input")
                                            input.form-control(type="text" placeholder="Введите название карточки" v-model="current_card.name" :class="{ 'is-invalid': $v.current_card.name.$error }")
                                            button.btn.btn-danger.me-2(type="button" @click="updateCardName")
                                                i.fas.fa-times
                                            .invalid-feedback(v-if="!$v.current_card.name.required") Обязательное поле.
                                            .invalid-feedback(v-if="!$v.current_card.name.maxLength") Макс. количество символов: {{ $v.current_card.name.$params.maxLength.max }}.
                                        h5.modal-title#exampleModalLabel(v-if="!show_card_name_input" style="cursor: pointer" @click="show_card_name_input = true") {{ current_card.name }}
                                            i.fas.fa-pencil-alt.ms-2
                                        button.btn-close(type="button" data-bs-dismiss="modal" aria-label="Close" @click="task_input_name_id = null")
                                    .modal-body
                                        .form-check.d-flex.justify-content-between.align-items-center(v-for="(task, index) in current_card.tasks")
                                            div
                                                form.d-inline(@submit.prevent="updateTask(current_card.tasks[index])" v-if="task_input_name_id == task.id")
                                                    input.d-inline.form-control(type="text" placeholder="Введите название задачи" v-model="current_card.tasks[index].name")
                                                label.form-check-label(for="my-input" v-else :for="'inlineCheckbox' + index") {{ task.name }}
                                                input#my-input.form-check-input(:id="'inlineCheckbox' + index" type="checkbox" v-model="current_card.tasks[index].is_done" @change="updateTask(current_card.tasks[index])")
                                            div
                                                button.btn(@click="changeTaskName(task.id)" v-if="task_input_name_id == null")
                                                    i.fas.fa-pencil-alt
                                                button.btn.btn-danger(type="button" @click="deleteTask(task.id)")
                                                    i.fas.fa-times
                                        form.mt-3(@submit.prevent="addNewTask")
                                            .form-group
                                                input.form-control.mt-2(type="text" placeholder="Введите название задачи" v-model="new_task_name" :class="{ 'is-invalid': $v.new_task_name.$error }")
                                                .invalid-feedback(v-if="!$v.new_task_name.required") Обязательное поле.
                                                .invalid-feedback(v-if="!$v.new_task_name.maxLength") Макс. количество символов: {{ $v.new_task_name.$params.maxLength.max }}.
                    button.btn.btn-danger.mt-3(type="button" @click="deleteDeskList(desk_list.id)") Удалить
        .text-center(v-if="loading")
            .spinner-border(role="status")
                span.visually-hidden Loading...
</template>

<script>
import { required, maxLength } from 'vuelidate/lib/validators'

export default {
    props: [
        'deskId'
    ],

    data() {
        return {
            name: null,
            errored: false,
            loading: true,
            desk_lists: null,
            desk_list_name: null,
            desk_list_input_id: null,
            card_names: [],
            current_card: [],
            show_card_name_input: false,
            new_task_name: null,
            task_input_name_id: null
        }
    },

    methods: {
        updateTask(task){
            axios.post('/api/V1/tasks/' + task.id, {
                    _method: 'PUT',
                    name: task.name,
                    is_done: task.is_done,
                    card_id:  task.card_id
                })
                .then(response => {
                    this.task_input_name_id = null
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
        },

        changeTaskName(id){
            this.task_input_name_id = id
        },

        deleteTask(id){
            this.task_input_name_id = null
            axios.post('/api/V1/tasks/' + id, {
                    _method: 'DELETE',
                })
                .then(response => {
                    this.$v.$reset()
                    this.getCard(this.current_card.id)
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
        },

        addNewTask(){
            this.$v.new_task_name.$touch()
            if(this.$v.new_task_name.$anyError){
                return;
            }

            axios.post('/api/V1/tasks', {
                    name: this.new_task_name,
                    card_id: this.current_card.id
                })
                .then(response => {
                    this.new_task_name = null
                    this.$v.$reset()
                    this.getCard(this.current_card.id)
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
        },

        updateCardName(){
            this.$v.current_card.name.$touch()
            if(this.$v.current_card.name.$anyError){
                return;
            }
            axios.post('/api/V1/cards/' + this.current_card.id, {
                    _method: 'PUT',
                    name: this.current_card.name,
                    desk_list_id: this.current_card.desk_list_id
                })
                .then(response => {
                    $('.modal-backdrop').hide();
                    this.show_card_name_input = false
                    this.$v.$reset()
                    this.getDeskLists()
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
        },

        getCard(id){
            axios.get('/api/V1/cards/' + id)
                .then(response => {
                    this.current_card = response.data.data
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
        },

        deleteCard(id){
            axios.post('/api/V1/cards/' + id, {
                    _method: 'DELETE'
                })
                .then(response => {
                    this.getDeskLists()
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
        },

        addNewCard(desk_list_id){
            this.$v.card_names.$each[desk_list_id].$touch() //вызов проверки валидации
            if(this.$v.card_names.$each[desk_list_id].$anyError){
                return;
            }

            axios.post('/api/V1/cards', {
                    name: this.card_names[desk_list_id],
                    desk_list_id
                })
                .then(response => {
                    this.$v.$reset()
                    this.getDeskLists()
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
        },

        updateDeskList(id, name){
            this.$v.desk_lists.$touch()
            if(this.$v.desk_lists.$anyError){
                return;
            }
            axios.post('/api/V1/desk-lists/' + id, {
                    _method: 'PUT',
                    name: name,
                })
                .then(response => {
                    this.desk_list_input_id = null
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
        },

        addNewDeskList(){
            this.errored = false
            this.$v.desk_list_name.$touch() //вызов проверки валидации
            if(this.$v.$anyError){
                return;
            }
            axios.post('/api/V1/desk-lists', {
                    name: this.desk_list_name,
                    desk_id: this.deskId
                })
                .then(response => {
                    this.$v.$reset()
                    this.desk_list_name = ''
                    this.getDeskLists()
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
            },

        getDeskLists(){
            this.desk_lists = []
            axios.get('/api/V1/desk-lists', {
                params: {
                    desk_id: this.deskId
                }
            })
            .then(response => {
                this.desk_lists = response.data.data
                let tmp = [];
                this.desk_lists.forEach(el => {
                    tmp[el.id] = ''
                })
                this.card_names = tmp;
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            }).finally(() => {
                this.loading = false
            })
        },

        saveName(){
            this.$v.name.$touch() //вызов проверки валидации
            if(this.$v.name.$anyError){
                return;
            }
            axios.post('/api/V1/desks/' + this.deskId, {
                _method: 'PUT', //laravel api для обработки запроса типа put должен в теле запроса получать данный параметр
                name: this.name,
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            }).finally(() => {
                this.loading = false
            }) 
        },

        deleteDeskList(id){
            axios.post('/api/V1/desk-lists/' + id, {
                _method: 'DELETE',
                })
                .then(response => {
                    this.desk_lists = []
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                }).finally(() => {
                    this.loading = false
                })
        }
    },

    mounted() {
        axios.get('/api/V1/desks/' + this.deskId)
            .then(response => {
                this.name = response.data.data.name
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            }).finally(() => {
                this.loading = false
            })
        this.getDeskLists()
    },

    validations: {
        name: {
            required,
            maxLength: maxLength(255)
        },

        desk_list_name: {
            required,
            maxLength: maxLength(255)
        },

        card_names: {
            $each: {
                required,
                maxLength: maxLength(255)
            }
        },

        current_card: {
            name: {
                required,
                maxLength: maxLength(255)
            }
        },

        desk_lists: {
            $each: {
                name: {
                    required,
                    maxLength: maxLength(255)
                }
            }
        },

        new_task_name: {
            required,
            maxLength: maxLength(255)
        }
    }
}

</script>