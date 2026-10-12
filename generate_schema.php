<?php

/**
 * Script Generador Automático de Modelos y Migraciones para Laravel
 * Ejecución: php generate_schema.php
 */

$files = [

    // =====================================================
    // MÓDULO SISTEMA / USUARIOS
    // =====================================================
    'database/migrations/2026_01_01_000001_create_users_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
PHP,

    'app/Models/User.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;use Illuminate\Database\Eloquent\Relations\HasMany;use Illuminate\Database\Eloquent\Relations\HasOne;use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    protected $fillable = ['name', 'email', 'role'];

    public function warehouse(): HasOne {
        return $this->hasOne(InventoryWarehouse::class, 'assigned_tech_id');
    }

    public function tickets(): HasMany {
        return $this->hasMany(FsmTicket::class, 'assigned_tech_id');
    }

    public function syncLogs(): HasMany {
        return $this->hasMany(PwaSyncLog::class);
    }

    public function statusHistories(): HasMany {
        return $this->hasMany(StatusHistory::class);
    }
}
PHP,

    // =====================================================
    // MÓDULO 1: CRM & CONTACTOS
    // =====================================================
    'database/migrations/2026_01_01_000002_create_crm_clients_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('business_name');
            $table->string('tax_id', 15)->unique();
            $table->string('tax_regime', 10);
            $table->string('zip_code', 10);
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('crm_clients');
    }
};
PHP,

    'app/Models/CrmClient.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;use App\Domains\Cotizaciones\Infraestructure\Eloquet\Models\CpqQuote;use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\HasMany;use Illuminate\Database\Eloquent\SoftDeletes;

class CrmClient extends Model {
    use SoftDeletes;

    protected $fillable = ['uuid', 'business_name', 'tax_id', 'tax_regime', 'zip_code', 'type'];

    public function branches(): HasMany { return $this->hasMany(CrmBranch::class, 'client_id'); }
    public function contacts(): HasMany { return $this->hasMany(CrmContact::class, 'client_id'); }
    public function quotes(): HasMany { return $this->hasMany(CpqQuote::class, 'client_id'); }
    public function projects(): HasMany { return $this->hasMany(FsmProject::class, 'client_id'); }
    public function invoices(): HasMany { return $this->hasMany(BillingInvoice::class, 'client_id'); }
    public function evidences(): HasMany { return $this->hasMany(Evidence::class, 'client_id'); }
}
PHP,

    'database/migrations/2026_01_01_000003_create_crm_branches_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->string('name');
            $table->text('address');
            $table->geometry('location', subtype: 'point', srid: 4326)->nullable();
            $table->timestamps();
        });

        DB::statement('CREATE INDEX idx_crm_branches_location ON crm_branches USING GIST (location);');
    }

    public function down(): void {
        Schema::dropIfExists('crm_branches');
    }
};
PHP,

    'app/Models/CrmBranch.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;use App\Domains\Clientes\Infraestructure\Eloquet\Models\CrmClient;use App\Domains\Cotizaciones\Infraestructure\Eloquet\Models\CpqQuote;use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmBranch extends Model {
    protected $fillable = ['client_id', 'name', 'address', 'location'];

    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function areas(): HasMany { return $this->hasMany(CrmArea::class, 'branch_id'); }
    public function contacts(): HasMany { return $this->hasMany(CrmContact::class, 'branch_id'); }
    public function quotes(): HasMany { return $this->hasMany(CpqQuote::class, 'branch_id'); }
}
PHP,

    'database/migrations/2026_01_01_000004_create_crm_areas_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('crm_branches')->onDelete('cascade');
            $table->string('name');
            $table->integer('floor_level')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('crm_areas');
    }
};
PHP,

    'app/Models/CrmArea.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmArea extends Model {
    public $timestamps = false;
    protected $fillable = ['branch_id', 'name', 'floor_level'];

    public function branch(): BelongsTo { return $this->belongsTo(CrmBranch::class, 'branch_id'); }
    public function tickets(): HasMany { return $this->hasMany(FsmTicket::class, 'area_id'); }
}
PHP,

    'database/migrations/2026_01_01_000005_create_crm_contacts_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('crm_branches')->onDelete('set null');
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('role_title', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('crm_contacts');
    }
};
PHP,

    'app/Models/CrmContact.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Clientes\Infraestructure\Eloquet\Models\CrmClient;use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmContact extends Model {
    public $timestamps = false;
    protected $fillable = ['client_id', 'branch_id', 'name', 'phone', 'email', 'role_title'];

    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(CrmBranch::class, 'branch_id'); }
}
PHP,

    // =====================================================
    // MÓDULO 2: CPQ, CATÁLOGOS E INVENTARIOS
    // =====================================================
    'database/migrations/2026_01_01_000006_create_catalog_products_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('catalog_products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('unit_price', 15, 4);
            $table->string('sat_product_key', 15);
            $table->string('sat_unit_key', 10);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('catalog_products');
    }
};
PHP,

    'app/Models/CatalogProduct.php' => <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogProduct extends Model {
    public $timestamps = false;
    protected $fillable = ['code', 'name', 'unit_cost', 'unit_price', 'sat_product_key', 'sat_unit_key'];

    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class, 'product_id'); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class, 'product_id'); }
}
PHP,

    'database/migrations/2026_01_01_000007_create_inventory_warehouses_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('assigned_tech_id')->nullable()->unique()->constrained('users')->onDelete('set null');
            $table->enum('type', ['CENTRAL', 'VAN', 'VIRTUAL']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_warehouses');
    }
};
PHP,

    'app/Models/InventoryWarehouse.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Users\Models\User\User;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryWarehouse extends Model {
    public $timestamps = false;
    protected $fillable = ['name', 'assigned_tech_id', 'type'];

    public function assignedTech(): BelongsTo { return $this->belongsTo(User::class, 'assigned_tech_id'); }
    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class, 'warehouse_id'); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class, 'warehouse_id'); }
}
PHP,

    'database/migrations/2026_01_01_000008_create_inventory_stocks_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('catalog_products')->onDelete('restrict');
            $table->decimal('stock_quantity', 12, 4)->default(0);
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['warehouse_id', 'product_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_stocks');
    }
};
PHP,

    'app/Models/InventoryStock.php' => <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model {
    const CREATED_AT = null;
    protected $fillable = ['warehouse_id', 'product_id', 'stock_quantity'];

    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
}
PHP,

    'database/migrations/2026_01_01_000009_create_inventory_movements_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('catalog_products')->onDelete('restrict');
            $table->enum('movement_type', ['INBOUND', 'OUTBOUND_TICKET', 'TRANSFER', 'ADJUSTMENT']);
            $table->decimal('quantity', 12, 4);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_movements');
    }
};
PHP,

    'app/Models/InventoryMovement.php' => <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model {
    public $timestamps = false;
    protected $fillable = ['warehouse_id', 'product_id', 'movement_type', 'quantity', 'reference_type', 'reference_id'];

    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
}
PHP,

    'database/migrations/2026_01_01_000010_create_cpq_quotes_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cpq_quotes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('cpq_quotes')->onDelete('set null');
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('restrict');
            $table->foreignId('branch_id')->nullable()->constrained('crm_branches')->onDelete('set null');
            $table->string('status', 30);
            $table->string('currency', 3)->default('MXN');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cpq_quotes');
    }
};
PHP,

    'app/Models/CpqQuote.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Clientes\Infraestructure\Eloquet\Models\CrmClient;use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;use Illuminate\Database\Eloquent\Relations\HasOne;

class CpqQuote extends Model {
    protected $fillable = ['uuid', 'parent_id', 'client_id', 'branch_id', 'status', 'currency', 'exchange_rate', 'total'];

    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(CrmBranch::class, 'branch_id'); }
    public function parentQuote(): BelongsTo { return $this->belongsTo(\App\Domains\Cotizaciones\Infraestructure\Eloquet\Models\CpqQuote::class, 'parent_id'); }
    public function childQuotes(): HasMany { return $this->hasMany(\App\Domains\Cotizaciones\Infraestructure\Eloquet\Models\CpqQuote::class, 'parent_id'); }
    public function items(): HasMany { return $this->hasMany(CpqQuoteItem::class, 'quote_id'); }
    public function project(): HasOne { return $this->hasOne(FsmProject::class, 'quote_id'); }
    public function statusHistories(): HasMany { return $this->hasMany(StatusHistory::class, 'quote_id'); }
}
PHP,

    'database/migrations/2026_01_01_000011_create_cpq_quote_items_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cpq_quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('cpq_quotes')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('catalog_products')->onDelete('restrict');
            $table->text('description');
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('unit_cost', 15, 4);
        });
    }

    public function down(): void {
        Schema::dropIfExists('cpq_quote_items');
    }
};
PHP,

    'app/Models/CpqQuoteItem.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Cotizaciones\Infraestructure\Eloquet\Models\CpqQuote;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpqQuoteItem extends Model {
    public $timestamps = false;
    protected $fillable = ['quote_id', 'product_id', 'description', 'quantity', 'unit_price', 'unit_cost'];

    public function quote(): BelongsTo { return $this->belongsTo(CpqQuote::class, 'quote_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
}
PHP,

    // =====================================================
    // MÓDULO 3: FSM OPERACIONES, MATERIALES EN CAMPO & PWA
    // =====================================================
    'database/migrations/2026_01_01_000012_create_fsm_projects_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->nullable()->unique()->constrained('cpq_quotes')->onDelete('restrict');
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('restrict');
            $table->string('code', 50)->unique();
            $table->string('status', 30);
            $table->decimal('total_budget', 15, 2);
            $table->decimal('financial_progress_percentage', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('fsm_projects');
    }
};
PHP,

    'app/Models/FsmProject.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Clientes\Infraestructure\Eloquet\Models\CrmClient;use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;

class FsmProject extends Model {
    protected $fillable = ['quote_id', 'client_id', 'code', 'status', 'total_budget', 'financial_progress_percentage'];

    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function milestones(): HasMany { return $this->hasMany(ProjectBillingMilestone::class, 'project_id'); }
    public function tickets(): HasMany { return $this->hasMany(FsmTicket::class, 'project_id'); }
    public function invoices(): HasMany { return $this->hasMany(BillingInvoice::class, 'project_id'); }
    public function evidences(): HasMany { return $this->hasMany(Evidence::class, 'project_id'); }
}
PHP,

    'database/migrations/2026_01_01_000013_create_project_billing_milestones_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_billing_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('fsm_projects')->onDelete('cascade');
            $table->string('name', 150);
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 15, 2);
            $table->string('status', 30);
            $table->date('due_date');
        });
    }

    public function down(): void {
        Schema::dropIfExists('project_billing_milestones');
    }
};
PHP,

    'app/Models/ProjectBillingMilestone.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectBillingMilestone extends Model {
    public $timestamps = false;
    protected $fillable = ['project_id', 'name', 'percentage', 'amount', 'status', 'due_date'];

    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function invoice(): HasOne { return $this->hasOne(BillingInvoice::class, 'milestone_id'); }
}
PHP,

    'database/migrations/2026_01_01_000014_create_fsm_tickets_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_uuid');
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained('crm_areas')->onDelete('set null');
            $table->foreignId('assigned_tech_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title', 200);
            $table->string('status', 30);
            $table->integer('lock_version')->default(1);
            $table->geometry('check_in_location', subtype: 'point', srid: 4326)->nullable();
            $table->geometry('check_out_location', subtype: 'point', srid: 4326)->nullable();
            $table->boolean('is_geofence_valid')->default(false);
            $table->decimal('economic_weight', 10, 2)->default(0.00);
            $table->timestamps();
        });

        DB::statement('CREATE INDEX idx_fsm_tickets_checkin ON fsm_tickets USING GIST (check_in_location);');
    }

    public function down(): void {
        Schema::dropIfExists('fsm_tickets');
    }
};
PHP,

    'app/Models/FsmTicket.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;use App\Domains\Users\Models\User\User;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;use Illuminate\Database\Eloquent\Relations\HasOne;

class FsmTicket extends Model {
    protected $fillable = [
        'client_uuid', 'project_id', 'area_id', 'assigned_tech_id',
        'title', 'status', 'lock_version', 'check_in_location',
        'check_out_location', 'is_geofence_valid', 'economic_weight'
    ];

    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function area(): BelongsTo { return $this->belongsTo(CrmArea::class, 'area_id'); }
    public function technician(): BelongsTo { return $this->belongsTo(User::class, 'assigned_tech_id'); }
    public function checklist(): HasOne { return $this->hasOne(FsmTicketChecklist::class, 'ticket_id'); }
    public function materials(): HasMany { return $this->hasMany(FsmTicketMaterial::class, 'ticket_id'); }
    public function evidences(): HasMany { return $this->hasMany(Evidence::class, 'ticket_id'); }
}
PHP,

    'database/migrations/2026_01_01_000015_create_fsm_ticket_checklists_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_ticket_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('fsm_tickets')->onDelete('cascade');
            $table->jsonb('data')->default('{}');
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('client_updated_at');
            $table->timestampTz('server_updated_at')->useCurrent();
        });

        DB::statement('CREATE INDEX idx_checklists_data_gin ON fsm_ticket_checklists USING GIN (data);');
    }

    public function down(): void {
        Schema::dropIfExists('fsm_ticket_checklists');
    }
};
PHP,

    'app/Models/FsmTicketChecklist.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsmTicketChecklist extends Model {
    public $timestamps = false;
    protected $fillable = ['ticket_id', 'data', 'completed_at', 'client_updated_at', 'server_updated_at'];

    protected $casts = [
        'data' => 'array',
        'completed_at' => 'datetime',
        'client_updated_at' => 'datetime',
        'server_updated_at' => 'datetime',
    ];

    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
}
PHP,

    'database/migrations/2026_01_01_000016_create_fsm_ticket_materials_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fsm_ticket_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('fsm_tickets')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('catalog_products')->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('restrict');
            $table->decimal('quantity_used', 12, 4);
            $table->decimal('unit_cost', 15, 4);
            $table->timestampTz('client_updated_at');
            $table->timestampTz('server_updated_at')->useCurrent();

            $table->unique(['ticket_id', 'product_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('fsm_ticket_materials');
    }
};
PHP,

    'app/Models/FsmTicketMaterial.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsmTicketMaterial extends Model {
    public $timestamps = false;
    protected $fillable = ['ticket_id', 'product_id', 'warehouse_id', 'quantity_used', 'unit_cost', 'client_updated_at', 'server_updated_at'];

    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
}
PHP,

    'database/migrations/2026_01_01_000017_create_pwa_sync_logs_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('pwa_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('batch_uuid');
            $table->jsonb('payload');
            $table->string('status', 30);
            $table->boolean('has_conflict')->default(false);
            $table->jsonb('conflict_details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('CREATE INDEX idx_pwa_payload_gin ON pwa_sync_logs USING GIN (payload);');
        DB::statement('CREATE INDEX idx_pwa_conflict_details_gin ON pwa_sync_logs USING GIN (conflict_details);');
    }

    public function down(): void {
        Schema::dropIfExists('pwa_sync_logs');
    }
};
PHP,

    'app/Models/PwaSyncLog.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Users\Models\User\User;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PwaSyncLog extends Model {
    public $timestamps = false;
    protected $fillable = ['user_id', 'batch_uuid', 'payload', 'status', 'has_conflict', 'conflict_details'];

    protected $casts = [
        'payload' => 'array',
        'conflict_details' => 'array',
        'has_conflict' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
PHP,

    // =====================================================
    // MÓDULO 4: FACTURACIÓN & FISCAL (CFDI 4.0)
    // =====================================================
    'database/migrations/2026_01_01_000018_create_tax_profiles_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tax_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('tax_id', 15)->unique();
            $table->string('legal_name');
            $table->string('tax_regime', 10);
            $table->string('zip_code', 10);
            $table->binary('facturapi_api_key_encrypted');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tax_profiles');
    }
};
PHP,

    'app/Models/TaxProfile.php' => <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxProfile extends Model {
    public $timestamps = false;
    protected $fillable = ['tax_id', 'legal_name', 'tax_regime', 'zip_code', 'facturapi_api_key_encrypted'];

    protected $casts = [
        'facturapi_api_key_encrypted' => 'encrypted',
    ];
}
PHP,

    'database/migrations/2026_01_01_000019_create_billing_invoices_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('restrict');
            $table->foreignId('milestone_id')->nullable()->unique()->constrained('project_billing_milestones')->onDelete('restrict');
            $table->foreignId('quote_id')->nullable()->constrained('cpq_quotes')->onDelete('restrict');
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('restrict');
            $table->string('facturapi_id', 100)->nullable();
            $table->uuid('fiscal_uuid')->nullable()->unique();
            $table->string('status', 30);
            $table->string('cfdi_use', 10);
            $table->string('tax_regime', 10);
            $table->string('receiver_zip_code', 10);
            $table->decimal('total', 15, 2);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('billing_invoices');
    }
};
PHP,

    'app/Models/BillingInvoice.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Clientes\Infraestructure\Eloquet\Models\CrmClient;use App\Domains\Productos\Infraestructure\Eloquent\Models\BillingInvoiceItem;use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoice extends Model {
    public $timestamps = false;
    protected $fillable = [
        'uuid', 'project_id', 'milestone_id', 'quote_id', 'client_id',
        'facturapi_id', 'fiscal_uuid', 'status', 'cfdi_use', 'tax_regime',
        'receiver_zip_code', 'total'
    ];

    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function milestone(): BelongsTo { return $this->belongsTo(ProjectBillingMilestone::class, 'milestone_id'); }
    public function items(): HasMany { return $this->hasMany(BillingInvoiceItem::class, 'invoice_id'); }
    public function paymentComplements(): HasMany { return $this->hasMany(BillingPaymentComplement::class, 'invoice_id'); }
    public function evidences(): HasMany { return $this->hasMany(Evidence::class, 'invoice_id'); }
}
PHP,

    'database/migrations/2026_01_01_000020_create_billing_invoice_items_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('billing_invoices')->onDelete('cascade');
            $table->string('sat_product_key', 15);
            $table->string('sat_unit_key', 10);
            $table->text('description');
            $table->decimal('total', 15, 2);
        });
    }

    public function down(): void {
        Schema::dropIfExists('billing_invoice_items');
    }
};
PHP,

    'app/Models/BillingInvoiceItem.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoiceItem extends Model {
    public $timestamps = false;
    protected $fillable = ['invoice_id', 'sat_product_key', 'sat_unit_key', 'description', 'total'];

    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
    public function taxes(): HasMany { return $this->hasMany(BillingInvoiceItemTax::class, 'invoice_item_id'); }
}
PHP,

    'database/migrations/2026_01_01_000021_create_billing_invoice_item_taxes_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_invoice_item_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_item_id')->constrained('billing_invoice_items')->onDelete('cascade');
            $table->enum('tax_type', ['IVA', 'ISR', 'IEPS']);
            $table->enum('factor_type', ['Tasa', 'Cuota', 'Exento']);
            $table->decimal('rate_or_fee', 6, 4);
            $table->decimal('amount', 15, 2);
        });
    }

    public function down(): void {
        Schema::dropIfExists('billing_invoice_item_taxes');
    }
};
PHP,

    'app/Models/BillingInvoiceItemTax.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Productos\Infraestructure\Eloquent\Models\BillingInvoiceItem;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInvoiceItemTax extends Model {
    public $timestamps = false;
    protected $fillable = ['invoice_item_id', 'tax_type', 'factor_type', 'rate_or_fee', 'amount'];

    public function item(): BelongsTo { return $this->belongsTo(BillingInvoiceItem::class, 'invoice_item_id'); }
}
PHP,

    'database/migrations/2026_01_01_000022_create_billing_payment_complements_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('billing_payment_complements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('billing_invoices')->onDelete('cascade');
            $table->string('facturapi_payment_id', 100);
            $table->decimal('amount', 15, 2);
            $table->string('status', 30);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('billing_payment_complements');
    }
};
PHP,

    'app/Models/BillingPaymentComplement.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingPaymentComplement extends Model {
    public $timestamps = false;
    protected $fillable = ['invoice_id', 'facturapi_payment_id', 'amount', 'status'];

    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
}
PHP,

    // =====================================================
    // MÓDULO 5: INTEGRIDAD REFERENCIAL Y AUDITORÍA
    // =====================================================
    'database/migrations/2026_01_01_000023_create_evidences_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_uuid');
            $table->foreignId('ticket_id')->nullable()->constrained('fsm_tickets')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('billing_invoices')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('crm_clients')->onDelete('cascade');
            $table->string('file_path', 500);
            $table->text('s3_key');
            $table->string('file_type', 50);
            $table->geometry('location', subtype: 'point', srid: 4326)->nullable();
            $table->boolean('is_out_of_bounds')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('ALTER TABLE evidences ADD CONSTRAINT chk_single_evidenciable CHECK (num_nonnulls(ticket_id, project_id, invoice_id, client_id) = 1);');
        DB::statement('CREATE INDEX idx_evidences_location ON evidences USING GIST (location);');
        DB::statement('CREATE INDEX idx_evidences_ticket_id ON evidences (ticket_id) WHERE ticket_id IS NOT NULL;');
        DB::statement('CREATE INDEX idx_evidences_project_id ON evidences (project_id) WHERE project_id IS NOT NULL;');
    }

    public function down(): void {
        Schema::dropIfExists('evidences');
    }
};
PHP,

    'app/Models/Evidence.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Clientes\Infraestructure\Eloquet\Models\CrmClient;use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model {
    public $timestamps = false;
    protected $fillable = [
        'client_uuid', 'ticket_id', 'project_id', 'invoice_id', 'client_id',
        'file_path', 's3_key', 'file_type', 'location', 'is_out_of_bounds'
    ];

    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
}
PHP,

    'database/migrations/2026_01_01_000024_create_status_histories_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->nullable()->constrained('cpq_quotes')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('fsm_projects')->onDelete('cascade');
            $table->foreignId('ticket_id')->nullable()->constrained('fsm_tickets')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('billing_invoices')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('from_status', 50);
            $table->string('to_status', 50);
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('ALTER TABLE status_histories ADD CONSTRAINT chk_single_statusable CHECK (num_nonnulls(quote_id, project_id, ticket_id, invoice_id) = 1);');
        DB::statement('CREATE INDEX idx_status_histories_ticket_id ON status_histories (ticket_id) WHERE ticket_id IS NOT NULL;');
    }

    public function down(): void {
        Schema::dropIfExists('status_histories');
    }
};
PHP,

    'app/Models/StatusHistory.php' => <<<'PHP'
<?php

namespace App\Models;

use App\Domains\Cotizaciones\Infraestructure\Eloquet\Models\CpqQuote;use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;use App\Domains\Users\Models\User\User;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusHistory extends Model {
    public $timestamps = false;
    protected $fillable = ['quote_id', 'project_id', 'ticket_id', 'invoice_id', 'user_id', 'from_status', 'to_status'];

    public function quote(): BelongsTo { return $this->belongsTo(CpqQuote::class, 'quote_id'); }
    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
PHP

];

// Proceso de creación automática de archivos
echo "Iniciando generación de arquitectura de base de datos...\n";
$createdCount = 0;

foreach ($files as $filePath => $content) {
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($filePath, trim($content));
    echo "  [OK] Creado: {$filePath}\n";
    $createdCount++;
}

echo "\n¡Proceso finalizado con éxito! Se crearon {$createdCount} archivos (Modelos y Migraciones).\n";
