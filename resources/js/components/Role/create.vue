<template>
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Role</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div v-if="errors !== null" class="alert content-alert content-alert--danger mt-4" role="alert">
                        <div class="content-alert__info">
                            <span class="content-alert__info-icon ua-icon-warning"></span>
                        </div>
                        <div class="content-alert__content">
                            <div class="content-alert__heading"></div>
                            <div class="content-alert__message">
                                <ul class="custom-alert__list" v-for="(values , key) in errors" :key="key">
                                    <li v-for="(value , index) in values" :key="index">{{ value }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="main-container">
                        <h3>Role Create Form</h3>
                        <form role="form" ref="addRoleFrom" name="addRoleFrom" v-on:submit.prevent="createRole">
                            <div class="from-block">
                               <div class="row">
                               <div class="col-sm-12">
                                 <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="role_name" v-model="form.role_name"
                                        placeholder="Role Name" required>
                                 </div>  
                               </div>               
                           
                               <div class="col-sm-12">
                                 <div class="form-group">
                                    <label for="email">Description</label>
                                    <input type="text" class="form-control" name="description" v-model="form.description"
                                        placeholder="Description" required>
                                 </div>  
                               </div>               
                           
                               <div class="col-sm-12">
                                 <div class="form-group">
                                    <label for="group">Group Name</label>
                                    <input type="text" class="form-control" name="group_name" v-model="form.group_name"
                                        placeholder="Group Name" required>
                                 </div>  
                               </div>               
                           
                               <div class="col-sm-12">
                                 <div class="form-group">
                                    <label for="type">Type Name</label>
                                    <input type="text" class="form-control" name="type_name" v-model="form.type_name"
                                        placeholder="Type Name" required>
                                 </div>  
                               </div>               
                            </div>

                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listRole()"
                                        class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                    <button type="submit" class="btn btn-success mb-2 mr-3">Create</button>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.text-red{
    color: red !important;
    padding-left: 10px;
}
</style>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                vendors: []
            }
        },
        mounted: function () {
        },
        methods: {
            createRole: function () {                
                axios.post('/role/create', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listRole();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
                //hidebtnLoader();
            },
            listRole: function () {
                this.$router.push({
                    path: '/roles'
                });
            }
        }
    }

</script>
