<?php


use Phinx\Seed\AbstractSeed;

class PostsSeeder extends AbstractSeed
{

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {

        $categories = $this->table('categories');
        if ($categories->exists()) {
            $categories = $this->adapter->fetchAll("SELECT * FROM categories");
        } else {
            $categories = [null];
        }

        $data = [];
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 2500; $i++) {
            $date = $faker->unixTime('now');
            $category = $categories[array_rand($categories)];
            $data[] = [
                'name'  => $faker->catchPhrase,
                'slug'  => $faker->slug,
                'category_id' => $category['id']??null,
                'content'  => $faker->text(5000),
                'created_at' => date('Y-m-d H:i:s', $date),
                'updated_at' => date('Y-m-d H:i:s', $date),
            ];
        }

        $this->table('posts')
            ->insert($data)
            ->save();
    }

}
