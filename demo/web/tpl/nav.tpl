<div class="nav-bg">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/">
                <img src="/misc/img/zdlh_logo.png">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item {if !$smarty.get.target}active{/if}">
                        <a class="nav-link" href="/">首页</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/car/new">新车</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/car/used">二手车</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/qa">常见问题</a>
                    </li>
                    <li class="nav-item dropdown {if stripos($smarty.get.target, 'applyfor') !== false}active{/if}">
                        <a class="nav-link dropdown-toggle" href="#" id="pos-sp" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        合作加盟
                        </a>
                        <div class="dropdown-menu" aria-labelledby="pos-sp">
                            <a class="dropdown-item" href="/applyfor/pos">POS门店</a>
                            <a class="dropdown-item" href="/applyfor/sp">代理商</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">登录</a>
                    </li>
                </ul>
            </div>
            <form class="form-inline">
                <select class="selectpicker" data-style="btn btn-sm bg-white btn-select-sm">
                    <option value="new">新车</option>
                    <option value="used">二手车</option>
                </select>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" style="padding: 5px 8px 6px 8px; margin-left: 10px;" placeholder="搜索品牌或车系">
                    <div class="input-group-append">
                        <button class="btn btn-dark"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </nav>
    </div>
</div>