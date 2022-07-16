<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectableBusinessSectorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_business_sectors')->upsert(
            [
                ["name" => "Abortion Policy/Anti-Abortion", "is_suggested"=> false],
                ["name" => "Abortion Policy/Pro-Abortion Rights", "is_suggested"=> false],
                ["name" => "Accountants", "is_suggested"=> false],
                ["name" => "Advertising/Public Relations", "is_suggested"=> false],
                ["name" => "Aerospace, Defense Contractors", "is_suggested"=> false],
                ["name" => "Agribusiness", "is_suggested"=> false],
                ["name" => "Agricultural Services & Products", "is_suggested"=> false],
                ["name" => "Agriculture", "is_suggested"=> false],
                ["name" => "Air Transport", "is_suggested"=> false],
                ["name" => "Air Transport Unions", "is_suggested"=> false],
                ["name" => "Airlines", "is_suggested"=> false],
                ["name" => "Alcoholic Beverages", "is_suggested"=> false],
                ["name" => "Alternative Energy Production & Services", "is_suggested"=> false],
                ["name" => "Architectural Services", "is_suggested"=> false],
                ["name" => "Attorneys/Law Firms", "is_suggested"=> false],
                ["name" => "Auto Dealers", "is_suggested"=> false],
                ["name" => "Auto Dealers, Japanese", "is_suggested"=> false],
                ["name" => "Auto Manufacturers", "is_suggested"=> false],
                ["name" => "Automotive", "is_suggested"=> false],
                ["name" => "Banking, Mortgage", "is_suggested"=> false],
                ["name" => "Banks, Commercial", "is_suggested"=> false],
                ["name" => "Banks, Savings & Loans", "is_suggested"=> false],
                ["name" => "Bars & Restaurants", "is_suggested"=> false],
                ["name" => "Beer, Wine & Liquor", "is_suggested"=> false],
                ["name" => "Books, Magazines & Newspapers", "is_suggested"=> false],
                ["name" => "Broadcasters, Radio/TV", "is_suggested"=> false],
                ["name" => "Builders/General Contractors", "is_suggested"=> false],
                ["name" => "Builders/Residential", "is_suggested"=> false],
                ["name" => "Building Materials & Equipment", "is_suggested"=> false],
                ["name" => "Building Trade Unions", "is_suggested"=> false],
                ["name" => "Business Associations", "is_suggested"=> false],
                ["name" => "Business Services", "is_suggested"=> false],
                ["name" => "Cable & Satellite TV Production & Distribution", "is_suggested"=> false],
                ["name" => "Candidate Committees", "is_suggested"=> false],
                ["name" => "Candidate Committees, Democratic", "is_suggested"=> false],
                ["name" => "Candidate Committees, Republican", "is_suggested"=> false],
                ["name" => "Car Dealers", "is_suggested"=> false],
                ["name" => "Car Dealers, Imports", "is_suggested"=> false],
                ["name" => "Car Manufacturers", "is_suggested"=> false],
                ["name" => "Casinos / Gambling", "is_suggested"=> false],
                ["name" => "Cattle Ranchers/Livestock", "is_suggested"=> false],
                ["name" => "Chemical & Related Manufacturing", "is_suggested"=> false],
                ["name" => "Chiropractors", "is_suggested"=> false],
                ["name" => "Civil Servants/Public Officials", "is_suggested"=> false],
                ["name" => "Clergy & Religious Organizations", "is_suggested"=> false],
                ["name" => "Clothing Manufacturing", "is_suggested"=> false],
                ["name" => "Coal Mining", "is_suggested"=> false],
                ["name" => "Colleges, Universities & Schools", "is_suggested"=> false],
                ["name" => "Commercial Banks", "is_suggested"=> false],
                ["name" => "Commercial TV & Radio Stations", "is_suggested"=> false],
                ["name" => "Communications/Electronics", "is_suggested"=> false],
                ["name" => "Computer Software", "is_suggested"=> false],
                ["name" => "Conservative/Republican", "is_suggested"=> false],
                ["name" => "Construction", "is_suggested"=> false],
                ["name" => "Construction Services", "is_suggested"=> false],
                ["name" => "Construction Unions", "is_suggested"=> false],
                ["name" => "Credit Unions", "is_suggested"=> false],
                ["name" => "Crop Production & Basic Processing", "is_suggested"=> false],
                ["name" => "Cruise Lines", "is_suggested"=> false],
                ["name" => "Cruise Ships & Lines", "is_suggested"=> false],
                ["name" => "Dairy", "is_suggested"=> false],
                ["name" => "Defense", "is_suggested"=> false],
                ["name" => "Defense Aerospace", "is_suggested"=> false],
                ["name" => "Defense Electronics", "is_suggested"=> false],
                ["name" => "Defense/Foreign Policy Advocates", "is_suggested"=> false],
                ["name" => "Democratic Candidate Committees", "is_suggested"=> false],
                ["name" => "Democratic Leadership PACs", "is_suggested"=> false],
                ["name" => "Democratic/Liberal", "is_suggested"=> false],
                ["name" => "Dentists", "is_suggested"=> false],
                ["name" => "Doctors & Other Health Professionals", "is_suggested"=> false],
                ["name" => "Drug Manufacturers", "is_suggested"=> false],
                ["name" => "Education", "is_suggested"=> false],
                ["name" => "E-Commerce", "is_suggested"=> false],
                ["name" => "Electric Utilities", "is_suggested"=> false],
                ["name" => "Electronics Manufacturing & Equipment", "is_suggested"=> false],
                ["name" => "Electronics, Defense Contractors", "is_suggested"=> false],
                ["name" => "Energy & Natural Resources", "is_suggested"=> false],
                ["name" => "Entertainment Industry", "is_suggested"=> false],
                ["name" => "Environment", "is_suggested"=> false],
                ["name" => "Farm Bureaus", "is_suggested"=> false],
                ["name" => "Farming", "is_suggested"=> false],
                ["name" => "Finance / Credit Companies", "is_suggested"=> false],
                ["name" => "Finance, Insurance & Real Estate", "is_suggested"=> false],
                ["name" => "Food & Beverage", "is_suggested"=> false],
                ["name" => "Food Processing & Sales", "is_suggested"=> false],
                ["name" => "Food Products Manufacturing", "is_suggested"=> false],
                ["name" => "Food Stores", "is_suggested"=> false],
                ["name" => "For-profit Education", "is_suggested"=> false],
                ["name" => "For-profit Prisons", "is_suggested"=> false],
                ["name" => "Foreign & Defense Policy", "is_suggested"=> false],
                ["name" => "Forestry & Forest Products", "is_suggested"=> false],
                ["name" => "Foundations, Philanthropists & Non-Profits", "is_suggested"=> false],
                ["name" => "Funeral Services", "is_suggested"=> false],
                ["name" => "Gambling & Casinos", "is_suggested"=> false],
                ["name" => "Gambling, Indian Casinos", "is_suggested"=> false],
                ["name" => "Garbage Collection/Waste Management", "is_suggested"=> false],
                ["name" => "Gas & Oil", "is_suggested"=> false],
                ["name" => "General Contractors", "is_suggested"=> false],
                ["name" => "Government Employee Unions", "is_suggested"=> false],
                ["name" => "Government Employees", "is_suggested"=> false],
                ["name" => "Gun Control", "is_suggested"=> false],
                ["name" => "Gun Rights", "is_suggested"=> false],
                ["name" => "Health", "is_suggested"=> false],
                ["name" => "Health Professionals", "is_suggested"=> false],
                ["name" => "Health Services/HMOs", "is_suggested"=> false],
                ["name" => "Hedge Funds", "is_suggested"=> false],
                ["name" => "HMOs & Health Care Services", "is_suggested"=> false],
                ["name" => "Home Builders", "is_suggested"=> false],
                ["name" => "Hospitals & Nursing Homes", "is_suggested"=> false],
                ["name" => "Hotels, Motels & Tourism", "is_suggested"=> false],
                ["name" => "Human Rights", "is_suggested"=> false],
                ["name" => "Ideological/Single-Issue", "is_suggested"=> false],
                ["name" => "Indian Gaming", "is_suggested"=> false],
                ["name" => "Industrial Unions", "is_suggested"=> false],
                ["name" => "Information Technology, Tech and Innovation", "is_suggested"=> false],
                ["name" => "Insurance", "is_suggested"=> false],
                ["name" => "Internet", "is_suggested"=> false],
                ["name" => "Israel Policy", "is_suggested"=> false],
                ["name" => "Labor", "is_suggested"=> false],
                ["name" => "Lawyers & Lobbyists", "is_suggested"=> false],
                ["name" => "Lawyers / Law Firms", "is_suggested"=> false],
                ["name" => "Leadership PACs", "is_suggested"=> false],
                ["name" => "Legal Services", "is_suggested"=> false],
                ["name" => "LGBTQIA Rights & Issues", "is_suggested"=> false],
                ["name" => "Liberal/Democratic", "is_suggested"=> false],
                ["name" => "Liquor, Wine & Beer", "is_suggested"=> false],
                ["name" => "Livestock", "is_suggested"=> false],
                ["name" => "Lobbyists", "is_suggested"=> false],
                ["name" => "Lodging / Tourism", "is_suggested"=> false],
                ["name" => "Logging, Timber & Paper Mills", "is_suggested"=> false],
                ["name" => "Manufacturing, Misc", "is_suggested"=> false],
                ["name" => "Marijuana", "is_suggested"=> false],
                ["name" => "Marijuana", "is_suggested"=> false],
                ["name" => "Marine Transport", "is_suggested"=> false],
                ["name" => "Meat processing & products", "is_suggested"=> false],
                ["name" => "Mechanical and electrical engineering", "is_suggested"=> false],
                ["name" => "Media and  culture", "is_suggested"=> false],
                ["name" => "Medical Supplies", "is_suggested"=> false],
                ["name" => "Mining", "is_suggested"=> false],
                ["name" => "Misc Business", "is_suggested"=> false],
                ["name" => "Misc Finance", "is_suggested"=> false],
                ["name" => "Misc Manufacturing & Distributing", "is_suggested"=> false],
                ["name" => "Misc Unions", "is_suggested"=> false],
                ["name" => "Miscellaneous Defense", "is_suggested"=> false],
                ["name" => "Miscellaneous Services", "is_suggested"=> false],
                ["name" => "Mortgage Bankers & Brokers", "is_suggested"=> false],
                ["name" => "Motion Picture Production & Distribution", "is_suggested"=> false],
                ["name" => "Music Production", "is_suggested"=> false],
                ["name" => "Natural Gas Pipelines", "is_suggested"=> false],
                ["name" => "Newspaper, Magazine & Book Publishing", "is_suggested"=> false],
                ["name" => "Non-profits, Foundations & Philanthropists", "is_suggested"=> false],
                ["name" => "Nurses", "is_suggested"=> false],
                ["name" => "Nursing Homes/Hospitals", "is_suggested"=> false],
                ["name" => "Nutritional & Dietary Supplements", "is_suggested"=> false],
                ["name" => "Oil & Gas", "is_suggested"=> false],
                ["name" => "Other", "is_suggested"=> false],
                ["name" => "Payday Lenders", "is_suggested"=> false],
                ["name" => "Pharmaceutical Manufacturing", "is_suggested"=> false],
                ["name" => "Pharmaceuticals / Health Products", "is_suggested"=> false],
                ["name" => "Phone Companies", "is_suggested"=> false],
                ["name" => "Physicians & Other Health Professionals", "is_suggested"=> false],
                ["name" => "Postal Unions", "is_suggested"=> false],
                ["name" => "Poultry & Eggs", "is_suggested"=> false],
                ["name" => "Power Utilities", "is_suggested"=> false],
                ["name" => "Printing & Publishing", "is_suggested"=> false],
                ["name" => "Private Equity & Investment Firms", "is_suggested"=> false],
                ["name" => "Pro-Israel", "is_suggested"=> false],
                ["name" => "Professional Sports, Sports Arenas & Related Equipment & Services", "is_suggested"=> false],
                ["name" => "Progressive/Democratic", "is_suggested"=> false],
                ["name" => "Public Employees", "is_suggested"=> false],
                ["name" => "Public Sector Unions", "is_suggested"=> false],
                ["name" => "Publishing & Printing", "is_suggested"=> false],
                ["name" => "Radio/TV Stations", "is_suggested"=> false],
                ["name" => "Railroads", "is_suggested"=> false],
                ["name" => "Real Estate", "is_suggested"=> false],
                ["name" => "Record Companies/Singers", "is_suggested"=> false],
                ["name" => "Recorded Music & Music Production", "is_suggested"=> false],
                ["name" => "Recreation / Live Entertainment", "is_suggested"=> false],
                ["name" => "Religious Organizations/Clergy", "is_suggested"=> false],
                ["name" => "Republican Candidate Committees", "is_suggested"=> false],
                ["name" => "Republican Leadership PACs", "is_suggested"=> false],
                ["name" => "Republican/Conservative", "is_suggested"=> false],
                ["name" => "Residential Construction", "is_suggested"=> false],
                ["name" => "Restaurants & Drinking Establishments", "is_suggested"=> false],
                ["name" => "Retail Sales", "is_suggested"=> false],
                ["name" => "Retired", "is_suggested"=> false],
                ["name" => "Savings & Loans", "is_suggested"=> false],
                ["name" => "Schools/Education", "is_suggested"=> false],
                ["name" => "Sea Transport", "is_suggested"=> false],
                ["name" => "Securities & Investment", "is_suggested"=> false],
                ["name" => "Special Trade Contractors", "is_suggested"=> false],
                ["name" => "Sports, Professional", "is_suggested"=> false],
                ["name" => "Steel Production", "is_suggested"=> false],
                ["name" => "Stock Brokers/Investment Industry", "is_suggested"=> false],
                ["name" => "Student Loan Companies", "is_suggested"=> false],
                ["name" => "Sugar Cane & Sugar Beets", "is_suggested"=> false],
                ["name" => "Teachers Unions", "is_suggested"=> false],
                ["name" => "Teachers/Education", "is_suggested"=> false],
                ["name" => "Telecom Services & Equipment", "is_suggested"=> false],
                ["name" => "Telephone Utilities", "is_suggested"=> false],
                ["name" => "Textiles", "is_suggested"=> false],
                ["name" => "Textiles ,Clothing and Fashion", "is_suggested"=> false],
                ["name" => "Timber, Logging & Paper Mills", "is_suggested"=> false],
                ["name" => "Tobacco", "is_suggested"=> false],
                ["name" => "Transportation", "is_suggested"=> false],
                ["name" => "Transportation Unions", "is_suggested"=> false],
                ["name" => "Trash Collection/Waste Management", "is_suggested"=> false],
                ["name" => "Trucking", "is_suggested"=> false],
                ["name" => "TV / Movies / Music", "is_suggested"=> false],
                ["name" => "TV Production", "is_suggested"=> false],
                ["name" => "Unions", "is_suggested"=> false],
                ["name" => "Unions, Airline", "is_suggested"=> false],
                ["name" => "Unions, Building Trades", "is_suggested"=> false],
                ["name" => "Unions, Industrial", "is_suggested"=> false],
                ["name" => "Unions, Misc", "is_suggested"=> false],
                ["name" => "Unions, Public Sector", "is_suggested"=> false],
                ["name" => "Unions, Public Service", "is_suggested"=> false],
                ["name" => "Unions, Teacher", "is_suggested"=> false],
                ["name" => "Unions, Transportation", "is_suggested"=> false],
                ["name" => "Universities, Colleges & Schools", "is_suggested"=> false],
                ["name" => "Vegetables & Fruits", "is_suggested"=> false],
                ["name" => "Venture Capital", "is_suggested"=> false],
                ["name" => "Waste Management", "is_suggested"=> false],
                ["name" => "Waste Management & Climate Change", "is_suggested"=> false],
                ["name" => "Wine, Beer & Liquor", "is_suggested"=> false],
                ["name" => "Women's Issues", "is_suggested"=> false],
            ],
            ["name", "is_suggested"]
        );
    }
}
