<style>
    .widget-user-header{
        background-position: center center !important;
        background-size: cover !important;
        height: 200px !important;
    }
</style>

<template>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-widget widget-user">
                    <div class="widget-user-header text-white" style="background: url('./images/background.jpg');">
                        <h3 class="widget-user-username">{{form.name}}</h3>
                        <h5 class="widget-user-desc">{{form.bio}}</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getUserProfile()" alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                            <h5 class="description-header">3,200</h5>
                            <span class="description-text">SALES</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                            <h5 class="description-header">13,000</h5>
                            <span class="description-text">FOLLOWERS</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4">
                            <div class="description-block">
                            <h5 class="description-header">35</h5>
                            <span class="description-text">PRODUCTS</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link  active show" href="#activity" data-toggle="tab">Activity</a></li>
                        <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="activity">
                                <h1>activity</h1>
                            </div>
                            <div class="tab-pane active show" id="settings">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-12 control-label">Name</label>
                                        <div class="col-sm-12">
                                        <input v-model="form.name" type="text" class="form-control" id="name" name="name" placeholder="Full Name" :class="{ 'is-invalid': form.errors.has('name') }">
                                        <has-error :form="form" field="name"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="col-sm-12 control-label">Email</label>
                                        <div class="col-sm-12">
                                        <input v-model="form.email" type="email" class="form-control" id="email" name="email" placeholder="Email" :class="{ 'is-invalid': form.errors.has('email') }">
                                        <has-error :form="form" field="email"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="bio" class="col-sm-12 control-label">Experience</label>
                                        <div class="col-sm-12">
                                        <textarea v-model="form.bio" class="form-control" id="bio" name="bio" placeholder="Experience" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                        <has-error :form="form" field="bio"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="photo" class="col-sm-12 control-label">Profile Photo</label>
                                        <div class="col-sm-12">
                                        <input  type="file" @change="convertPhoto" name="photo" class="form-input" id="photo">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="col-sm-12 control-label">Password(leave empty if not changing)</label>
                                        <div class="col-sm-12">
                                        <input v-model="form.password" type="password" name="password" class="form-control" id="password" :class="{ 'is-invalid': form.errors.has('email') }">
                                        <has-error :form="form" field="password"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-12">
                                        <button @click.prevent="updateProfile" type="submit" class="btn btn-success">Edit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        mounted() {
            console.log('Component mounted.')
        },
        methods:{
            getUserProfile(){
                return "/images/profile/"+this.form.photo;
            },
            updateProfile(){
                this.$Progress.start();
                this.form.put('api/profile')
                .then(()=>{
                    izitoast.show({
                        title: 'Success',
                        message: 'User Updated!',
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
            },
            convertPhoto(e){
                let file = e.target.files[0];
                console.log(file);
                let reader = new FileReader();
                if(file['size'] < 2111775){
                        reader.onloadend = (file) => {
                            // console.log('RESULT', reader.result)
                            this.form.photo = reader.result;
                        }
                        reader.readAsDataURL(file);
                }else{
                    swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Maximum file size is 2MB !'
                    });
                    $('#photo').val('');
                    this.form.photo = "profile.png";
                }
            }
        },
        created(){
            axios.get("api/profile").then(({ data }) => (this.form.fill(data)));
        }
    }
</script>
