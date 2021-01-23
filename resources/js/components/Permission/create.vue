<template>
    <div class="container-fluid">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h3>Create Permission</h3>
                    </div>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div v-if="errors !== null"
                    class="alert alert-danger alert-fill alert-close alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong>Create Permission Faild!</strong>
                    <ul v-for="(values , key) in errors" :key="key">
                        <li v-for="(value , index) in values" :key="index">{{ value }}</li>
                    </ul>
                </div>
                <section class="card">
                    <div class="card-block">
                        <h5 class="with-border">Create Permission Form</h5>
                        <form role="form" ref="addPermissionForm" name="addPermissionForm" v-on:submit.prevent="createPermission">
                            <div class="row">
                               <div class="col-sm-12">
                                 <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="permission_name" v-model="form.permission_name"
                                        placeholder="Permission Name" required>
                                 </div>  
                               </div>               
                           
                               <div class="col-sm-12">
                                 <div class="form-group">
                                    <label for="description">Description</label>
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
                            <hr />
                            <div class="form-group text-right m-b-0">
                                <button type="button" @click="listPermission()"
                                    class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                <button type="submit" class="btn btn-success mb-2 mr-3">Create</button>
                            </div>
                        </form>
                    </div>
                </section>
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
            }
        },
        mounted: function () {
        },
        methods: {
            
            createPermission: function () {                
                axios.post('/admin/permission/create', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listPermission();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
                //hidebtnLoader();
            },
            listPermission: function () {
                this.$router.push({
                    path: '/admin/permissions'
                });
            }
        }
    }

</script>
