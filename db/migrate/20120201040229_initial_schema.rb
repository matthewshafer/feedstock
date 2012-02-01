class InitialSchema < ActiveRecord::Migration
  def self.up
  	create_table :catstags, :primary_key => :PrimaryKey do |t|
  		t.integer :PrimaryKey
  		t.string :Name, :null => false
  		t.string :URIName, :null => false
  		t.integer :Type, :null => false
  		t.integer :SubCat, {:null => false, :default => -1}
  	end
  	add_index :catstags, :URIName, :name => "URINameINDEX"
  	add_index :catstags, :Type, :name => "TypeIndex"
  	add_index :catstags, [:Type, :PrimaryKey], :name => "TypePrimaryIndex"

  	create_table :pages, :primary_key => :PrimaryKey do |t|
  		t.integer :PrimaryKey
  		t.string :Title, :null => false
  		t.string :NiceTitle, :null => false
  		t.string :URI, :null => false
  		t.text :PageData, {:null => false, :limit => 16777216}
  		t.integer :Author, :null => false
  		t.datetime :Date, :null =>false
  		t.string :themeFile, {:null => false, :limit => 50}
  		t.integer :Draft, {:null => false, :default = 1}
  		t.string :Corral, {:default = nil, :limit => 50}
  	end
  	add_index :pages, :Corral, :name => "CorralIndex"
  	add_index :pages, [:URI, :Draft], :name => "URIDraftINDEX"
  end

  def self.down
    raise ActiveRecord::IrreversibleMigration
  end
end
