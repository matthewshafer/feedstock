class InitialSchema < ActiveRecord::Migration
  def self.up
  	create_table :catstags, :primary_key => :PrimaryKey do |t|
  		t.integer :PrimaryKey
  		t.string :Name, :null => false
  		t.string :URIName, :null => false
  		t.integer :Type, :null => false
  		t.integer :SubCat, {:null => false, :default => -1}
  	end
  	add_index :catstags, :URIName, :name => "catstags_Index_on_URIName"
  	add_index :catstags, :Type, :name => "catstags_Index_on_Type"
  	add_index :catstags, [:Type, :PrimaryKey], :name => "catstags_Index_on_Type_and_PrimaryKey"

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
  	add_index :pages, :Corral, :name => "pages_Index_on_Corral"
  	add_index :pages, [:URI, :Draft], :name => "pages_Index_on_URI_and_Draft"

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
    add_index :posts, :URI, :name => "posts_Index_on_URI"
    add_index :posts, [:Draft, :Date], :name => "posts_Index_on_Draft_and_Date"
    add_index :posts, [:Draft, :PrimaryKey], :name => "posts_Index_on_Draft_and_PrimaryKey"

    create_table :posts_tax, :id => false do |t|
      t.integer :PostID, :null => false
      t.integer :CatTagID, :null => false
    end
    add_index :posts_tax, :PostID, :name => "posts_tax_Index_on_PostID"
    add_index :posts_tax, :CatTagID, :name => "posts_tax_Index_on_CatTagID"
    add_index :posts_tax, [:PostID, :CatTagID], :name => "posts_tax_Index_on_PostID_and_CatTagID"

    create_table :snippet, :primary_key => :PrimaryKey do |t|
      t.integer :PrimaryKey
      t.string :Name, :null => false
      t.text :SnippetData, {:null => false, :limit => 16777216}
    end
    add_index :snippet, :Name, :name => "snippet_Index_on_Name"

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
