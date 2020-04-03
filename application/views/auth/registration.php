<div class="container">

  <div class="card o-hidden border-0 shadow-lg my-5 col-lg-6 mx-auto">
    <div class="card-body p-0">
      <!-- Nested Row within Card Body -->
      <div class="row">
        <div class="col-lg">
          <div class="p-4">
            <div class="text-center">
              <h1 class="h4 text-gray-900 mb-3">Create an Account!</h1>
            </div>
            <form class="user" method="post" action="<?= base_url('auth/registration');?>">
              <div class="form-group">
                <input type="text" class="form-control form-control-user" id="name" name="name"
                  placeholder="Enter your name..." value="<?= set_value('name'); ?>">
                <?= form_error('name', '<small class="text-danger pl-3">', '</small>') ; ?>
              </div>
              <div class="form-group">
                <input type="text" class="form-control form-control-user" id="email" name="email"
                  placeholder="Email Address" value="<?= set_value('email'); ?>">
                <?= form_error('email', '<small class="text-danger pl-3">', '</small>') ; ?>
              </div>
              <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                  <input type="password" class="form-control form-control-user" id="password1" name="password1"
                    placeholder="Password">
                  <?= form_error('password1', '<small class="text-danger pl-3">', '</small>') ; ?>
                </div>
                <div class="col-sm-6">
                  <input type="password" class="form-control form-control-user" id="password2" name="password2"
                    placeholder="Repeat Password">
                </div>
              </div>
              <button type="submit" class="btn btn-primary btn-user btn-block">
                Register Account
              </button>
              <hr>
              <button type="submit" class="btn btn-google btn-user btn-block">
                <i class="fab fa-google fa-fw"></i> Register with Google
              </button>
              <button type="submit" class="btn btn-facebook btn-user btn-block">
                <i class="fab fa-facebook-f fa-fw"></i> Register with Facebook
              </button>
            </form>
            <hr>
            <div class="text-center">
              <a class="small" href="<?= base_url('auth/forgotpassword'); ?>">Forgot Password</a>
            </div>
            <div class="text-center">
              <a class="small" href="<?= base_url('auth');?>">Already have an account? Login!</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>