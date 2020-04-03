<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-lg-6">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 ">Change Your Password for</h1>
                    <h5 class="mb-2">
                      <?= $this->session->userdata('reset_email'); ?>
                    </h5>
                    <p class="mb-4">We get it, stuff happens. Just enter your email address below and we'll send you
                      a
                      link to reset your password!</p>
                  </div>
                  <?= $this->session->flashdata('message') ?>
                  <form class="user" method="post" action="<?= base_url('auth/changepassword'); ?>">
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="password1" 
                      name ="password1" placeholder="Enter password..." value="<?= set_value('email'); ?>">
                      <?= form_error('password1', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="password2" 
                      name ="password2" placeholder="Enter password..." value="<?= set_value('email'); ?>">
                      <?= form_error('password2', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>

                    <button type="submit" class="btn btn-info btn-user btn-block">
                      Change Password
                    </button>
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="<?= base_url('auth'); ?> ">Back to Login!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>