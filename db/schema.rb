# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 20120201040229) do

  create_table "catstags", :primary_key => "PrimaryKey", :force => true do |t|
    t.string  "Name",                    :null => false
    t.string  "URIName",                 :null => false
    t.integer "Type",                    :null => false
    t.integer "SubCat",  :default => -1, :null => false
  end

  add_index "catstags", ["Type", "PrimaryKey"], :name => "catstags_Index_on_Type_and_PrimaryKey"
  add_index "catstags", ["Type"], :name => "catstags_Index_on_Type"
  add_index "catstags", ["URIName"], :name => "catstags_Index_on_URIName"

  create_table "pages", :primary_key => "PrimaryKey", :force => true do |t|
    t.string   "Title",                                        :null => false
    t.string   "NiceTitle",                                    :null => false
    t.string   "URI",                                          :null => false
    t.text     "PageData",  :limit => 16777216,                :null => false
    t.integer  "Author",                                       :null => false
    t.datetime "Date",                                         :null => false
    t.string   "themeFile", :limit => 50,                      :null => false
    t.integer  "Draft",                         :default => 0, :null => false
    t.string   "Corral",    :limit => 50
  end

  add_index "pages", ["Corral"], :name => "pages_Index_on_Corral"
  add_index "pages", ["URI", "Draft"], :name => "pages_Index_on_URI_and_Draft"

  create_table "posts", :primary_key => "PrimaryKey", :force => true do |t|
    t.string   "Title",                                        :null => false
    t.string   "NiceTitle",                                    :null => false
    t.string   "URI",                                          :null => false
    t.text     "PostData",  :limit => 16777216,                :null => false
    t.text     "Category",                                     :null => false
    t.text     "Tags",                                         :null => false
    t.integer  "Author",                                       :null => false
    t.datetime "Date",                                         :null => false
    t.string   "themeFile", :limit => 50,                      :null => false
    t.integer  "Draft",                         :default => 0, :null => false
  end

  add_index "posts", ["Draft", "Date"], :name => "posts_Index_on_Draft_and_Date"
  add_index "posts", ["Draft", "PrimaryKey"], :name => "posts_Index_on_Draft_and_PrimaryKey"
  add_index "posts", ["URI"], :name => "posts_Index_on_URI"

  create_table "posts_tax", :id => false, :force => true do |t|
    t.integer "PostID",   :null => false
    t.integer "CatTagID", :null => false
  end

  add_index "posts_tax", ["CatTagID"], :name => "posts_tax_Index_on_CatTagID"
  add_index "posts_tax", ["PostID", "CatTagID"], :name => "posts_tax_Index_on_PostID_and_CatTagID"
  add_index "posts_tax", ["PostID"], :name => "posts_tax_Index_on_PostID"

  create_table "snippet", :primary_key => "PrimaryKey", :force => true do |t|
    t.string "Name",                            :null => false
    t.text   "SnippetData", :limit => 16777216, :null => false
  end

  add_index "snippet", ["Name"], :name => "snippet_Index_on_Name"

  create_table "users", :force => true do |t|
    t.text    "loginName",                                    :null => false
    t.text    "displayName",                                  :null => false
    t.string  "PasswordHash",  :limit => 512,                 :null => false
    t.string  "Salt",                                         :null => false
    t.integer "Permissions",                  :default => 99, :null => false
    t.integer "CanAdminUsers",                :default => 0,  :null => false
    t.string  "CookieVal",     :limit => 512,                 :null => false
  end

end
