<template>
    <div class="container mt-3">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header" style="height:55px;">
                <h3 class="card-title">Users Table</h3>
                <div class="card-tools">
                    <button @click="newModal" class="btn btn-success">Create User <i class="fas fa-user-plus fa-fw"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registered At</th>
                    <th width="15">Modify</th>
                  </tr>
                  <tr v-for="user in users" :key="user.id" >
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.type | upText }}</td>
                    <td>{{ user.created_at | myDate}}</td>
                    <td>
                        <a href="#" @click.prevent="editModal(user)"><i class="fa fa-edit blue"></i></a> |
                        <a href="#" @click.prevent="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>


<!-- The Modal -->
<div class="modal fade" id="addModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 v-show="!editmode" class="modal-title">Add New User</h4>
        <h4 v-show="editmode" class="modal-title">Edit User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form @submit.prevent="editmode ? updateUser():createUser()">
      <div class="modal-body">
        <div class="form-group">
            <input v-model="form.name" type="text" id="name" name="name"
               placeholder="Full Name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
            <has-error :form="form" field="name"></has-error>
        </div>
        <div class="form-group">
            <input v-model="form.email" type="email" id="email" name="email"
               placeholder="Email Address" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
            <has-error :form="form" field="email"></has-error>
        </div>
        <div class="form-group">
            <input v-model="form.password" type="password" id="password" name="password"
               placeholder="********" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
            <has-error :form="form" field="password"></has-error>
        </div>
        <div class="form-group">
            <select v-model="form.type" class="form-control" id="type" name="type" :class="{ 'is-invalid': form.errors.has('type') }">
                <option value="">Select Role For User</option>
                <option value="admin">Admin</option>
                <option value="user">Standart User</option>
                <option value="author">Author</option>
            </select>
            <has-error :form="form" field="type"></has-error>
        </div>
        <div class="form-group">
            <textarea name="bio" id="bio" class="form-control" v-model="form.bio" :class="{ 'is-invalid': form.errors.has('bio') }" placeholder="Short bio for user (Optional)">
            </textarea>
            <has-error :form="form" field="bio"></has-error>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button v-show="editmode" type="submit" class="btn btn-success">Edit</button>
        <button v-show="!editmode" type="submit" class="btn btn-success">Save</button>
      </div>
    </form>
    </div>
  </div>
</div>
<!-- Close Modal -->




    </div>
</template>
<script>
    export default {
        data(){
            return {
                editmode : false,
                users : {},
                form : new Form({
                id: '',
                name : '',
                email : '',
                password : '',
                bio : '',
                type : '',
                photo : ''
                })
            }
        },
        methods : {
            updateUser(){
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id).then(()=>{
                    Fire.$emit('AfterAction');
                    $('#addModal').modal('hide');
                    izitoast.show({
                        title: 'Success',
                        message: 'User Updated!',
                        color: 'green',
                        position: 'topRight'
                    });
                    this.$Progress.finish();
                }).catch(()=>{
                    izitoast.show({
                        title: 'Error',
                        message: 'Something Error!',
                        color: 'red',
                        position: 'topRight'
                    });
                    this.$Progress.fail();
                })
            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addModal').modal('show');
                this.form.fill(user);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addModal').modal('show');
            },
            deleteUser(id){
                swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if(result.value){
                                this.$Progress.start();
                                this.form.delete('api/user/'+id).then(()=>{
                                    Fire.$emit('AfterAction');
                                    swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    );
                                    this.$Progress.finish();
                                }).catch(()=>{
                                    swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Something went wrong!'
                                    });
                                    this.$Progress.fail();
                                })
                            }
                        })
            },
            loadUsers(){
                axios.get("api/user").then(({ data }) => (this.users = data.data));
            },
            createUser(){
                this.$Progress.start();
                this.form.post('api/user')
                .then(()=>{
                    Fire.$emit('AfterAction');
                    $('#addModal').modal('hide');
                    izitoast.show({
                        title: 'Success',
                        message: 'User Created!',
                        color: 'green',
                        position: 'topRight'
                    });
                    this.$Progress.finish();
                })
                .catch(()=>{
                    izitoast.show({
                        title: 'Error',
                        message: 'Something Error!',
                        color: 'red',
                        position: 'topRight'
                    });
                    this.$Progress.fail();
                })
            }
        },
        created() {
           this.loadUsers();
           Fire.$on('AfterAction', () => {
               this.loadUsers();
           });
        }
    }
</script>
