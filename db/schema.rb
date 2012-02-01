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

  add_index "catstags", ["Type", "PrimaryKey"], :name => "TypePrimaryIndex"
  add_index "catstags", ["Type"], :name => "TypeIndex"
  add_index "catstags", ["URIName"], :name => "URINameINDEX"

  create_table "pages", :primary_key => "PrimaryKey", :force => true do |t|
    t.string "Title",                         :null => false
    t.string "NiceTitle",                     :null => false
    t.string "URI",                           :null => false
    t.text   "PageData",  :limit => 16777216, :null => false
  end

end
