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
  		t.integer :Draft, {:null => false, :default => 0}
  		t.string :Corral, {:default => nil, :limit => 50}
  	end
  	add_index :pages, :Corral, :name => "CorralIndex"
  	add_index :pages, [:URI, :Draft], :name => "URIDraftINDEX"

    create_table :posts, :primary_key => :PrimaryKey do |t|
      t.integer :PrimaryKey
      t.string :Title, :null => false
      t.string :NiceTitle, :null => false
      t.string :URI, :null => false
      t.text :PostData, {:null => false, :limit => 16777216}
      t.text :Category, :null => false
      t.text :Tags, :null => false
      t.integer :Author, :null => false
      t.datetime :Date, :null => false
      t.string :themeFile, {:null => false, :limit => 50}
      t.integer :Draft, {:null => false, :default => 0}
    end
    add_index :posts, :URI, :name => "URIINDEX"
    add_index :posts, [:Draft, :Date], :name => "Date_Draft"
    add_index :posts, [:Draft, :PrimaryKey], :name => "DraftPrimaryIndex"

    create_table :posts_tax, :id => false do |t|
      t.integer :PostID, :null => false
      t.integer :CatTagID, :null => false
    end
    add_index :posts_tax, :PostID, :name => "PostIDindex"
    add_index :posts_tax, :CatTagID, :name => "CatTagIDindex"
    add_index :posts_tax, [:PostID, :CatTagID], :name => "PostCatTagIndex"

    create_table :snippet, :primary_key => :PrimaryKey do |t|
      t.integer :PrimaryKey
      t.string :Name, :null => false
      t.text :SnippetData, {:null => false, :limit => 16777216}
    end
    add_index :snippet, :Name, :name => "NameIndex"

    create_table :users, :primary_key => :id do |t|
      t.integer :id
      t.text :loginName, :null => false
      t.text :displayName, :null => false
      t.string :PasswordHash, {:null => false, :limit => 512}
      t.string :Salt, :null => false
      t.integer :Permissions, {:null => false, :default => 99}
      t.integer :CanAdminUsers, {:null => false, :default => 0}
      t.string :CookieVal, {:null => false, :limit => 512}
    end


  end

  def self.down
    raise ActiveRecord::IrreversibleMigration
  end
end
