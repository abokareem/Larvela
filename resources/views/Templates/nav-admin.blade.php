<!-- Larvel nav-admin template -->
<nav class="navbar navbar-default navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/dashboard">Larvela</a>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="/admin/orders">Orders <span class="sr-only">(current)</span></a></li>
        <li><a href="/admin/products">Products</a></li>
        <li><a href="/admin/customers">Customers</a></li>
        <li><a href="/admin/advert">Adverts</a></li>
        <li><a href="/admin/seo">Seo</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">System <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/admin/stores">Stores</a></li>
            <li><a href="/admin/settings">System Settings</a></li>
            <li><a href="/admin/images">Image Management</a></li>
            <li><a href="/admin/mailrun/control">Mailout</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" action="/admin/search">
        <div class="form-group">
          <input type="text" name="search" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
	    {!! Form::token() !!}
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/admin/subscriptions">Subscriptions</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Misc <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/admin/categories">Categories</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/admin/templates">Templates</a></li>
            <li><a href="/admin/actions">Template Actions</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/admin/attributes">Product Attributes</a></li>
            <li><a href="/admin/producttypes">Product Types</a></li>
          </ul>
          <li><a href="/auth/logout">Logout</a></li>
        </li>
      </ul>
      </div>
    </div>
</nav>
<!-- END:nav-admin -->
