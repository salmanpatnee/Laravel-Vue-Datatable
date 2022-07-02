<template>
    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-start">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="per-page" class="col-form-label">Per Page</label>
                    </div>
                    <div class="col-auto">
                        <select class="form-select" v-model="paginate">
                            <option v-for="perPage in paginateLengthMenu" :key="perPage" :value="perPage">{{ perPage }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col d-flex justify-content-center">
                <a :href="exportUrl" class="btn btn-outline-success"
                    :class="selectedRecords < 1 ? 'disabled' : ''">Export to
                    Excel</a>
            </div>
            <div class="col d-flex justify-content-end">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" v-model.trim.lazy="search" class="form-control"
                            placeholder="Search Students">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3" v-show="selectedRecords > 0">
            <p v-if="isAllSelected" class="text-start">You are selecting all <b>{{ selectedRecords }}</b>
                records.
            </p>
            <p v-else class="text-start">You have selected <b>{{ selectedRecords }}</b> record(s), Do you want to select
                all
                <b v-if="dataLoaded">{{ total }}</b> records?
                <a @click.prevent="selectAllRecords" href="">Select all</a>
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="table-responsive mt-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="checkAll">
                                    </div>
                                </th>
                                <th scope="col">
                                    <a href="" @click.prevent="sort('name')">Name</a>
                                    <span v-show="sortOrder == 'desc' && orderBy == 'name'">&uarr;</span>
                                    <span v-show="sortOrder == 'asc' && orderBy == 'name'">&darr;</span>
                                </th>
                                <th scope="col">
                                    <a href="" @click.prevent="sort('email')">Email</a>
                                    <span v-show="sortOrder == 'desc' && orderBy == 'email'">&uarr;</span>
                                    <span v-show="sortOrder == 'asc' && orderBy == 'email'">&darr;</span>
                                </th>
                                <th scope="col">Phone</th>
                                <th scope="col">Class</th>
                                <th scope="col">Section</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="student in students.data" :key="student.id"
                                :class="isSelectedRow(student.id) ? 'table-primary' : ''">
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" :value="student.id"
                                            v-model="checked">
                                    </div>
                                </th>
                                <td>{{ student.name }}</td>
                                <td>{{ student.email }}</td>
                                <td>{{ student.phone }}</td>
                                <td>{{ student.class }}</td>
                                <td>{{ student.section }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6 d-flex justify-content-start">
                <p v-if="dataLoaded" class="text-start">Showing {{ page }} to {{ students.meta.last_page }} of {{
                        total
                }}
                    record(s)</p>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <Pagination :data="students" @pagination-change-page="getStudents" />
            </div>
        </div>
    </div>
</template>

<script>

import LaravelVuePagination from 'laravel-vue-pagination';

export default {
    data() {
        return {
            baseEndPoint: '/api/students',
            students: {},
            checked: [],
            checkAll: false,
            dataLoaded: false,
            exportUrl: '',
            orderBy: 'created_at',
            page: 1,
            paginateLengthMenu: [10, 25, 50, 100, 250, 500],
            paginate: 10,
            search: '',
            isAllSelected: false,
            sortOrder: 'desc',
        }
    },
    computed: {
        total() {
            return this.students.meta.total;
        },
        selectedRecords() {
            return this.checked.length;
        }
    },
    watch: {
        paginate: function ($value) {
            this.getStudents();
        },
        search: function ($value) {
            this.getStudents();
        },
        checkAll: function ($value) {
            this.checked = [];
            this.isAllSelected = false;

            if ($value) {
                this.students.data.map(student => {
                    this.checked.push(student.id);
                });
                this.checkAll = true;
            }
        },
        checked: function ($value) {
            this.exportUrl = this.baseEndPoint + "/export/" + this.checked;
        }
    },
    components: {
        'Pagination': LaravelVuePagination
    },

    methods: {
        getStudents(page = 1) {
            this.page = page;
            axios.get(this.baseEndPoint
                + '?page=' + page
                + '&paginate=' + this.paginate
                + '&search=' + this.search
                + '&sortOrder=' + this.sortOrder
                + '&orderBy=' + this.orderBy
            ).then(response => {
                this.students = response.data;
                this.dataLoaded = true;
            }).catch(error => {
                this.dataLoaded = false;
                console.log('Error: ' + error);
            });
        },
        isSelectedRow(id) {
            return this.checked.includes(id);
        },
        selectAllRecords() {
            axios.get(this.baseEndPoint + '/selectAll').then(response => {
                this.checked = response.data;
                this.isAllSelected = true;
            }).catch(error => {
                console.log('Error: ' + error);
            });
        },
        sort(col) {
            this.orderBy = col;
            this.sortOrder = this.sortOrder == 'desc' ? 'asc' : 'desc';
            this.getStudents();
        },

    },
    mounted() {
        this.getStudents();
    }
}
</script>
