<?php
class ModelExtensionMazaSystem extends Model {
        public function restore($sql) {
		foreach (explode(";\n", $sql) as $sql) {
			$sql = trim($sql);

			if ($sql) {
                            $this->db->query(str_replace(' `oc_', ' `' . DB_PREFIX, $sql));
			}
		}
	}
        
	public function backup($tables) {
		$output = '';

		foreach (array_unique($tables) as $table) {
                        $output .= 'TRUNCATE TABLE `oc_' . $table . '`;' . "\n\n";

                        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $table . "`");

                        foreach ($query->rows as $result) {
                                // For opencart 4 import
                                if ($table == 'product') {
                                        unset($result['viewed']);
                                }

                                $fields = '';

                                foreach (array_keys($result) as $value) {
                                        $fields .= '`' . $value . '`, ';
                                }

                                $values = '';

                                foreach (array_values($result) as $value) {
                                        $value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
                                        $value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
                                        $value = str_replace('\\', '\\\\',	$value);
                                        $value = str_replace('\'', '\\\'',	$value);
                                        $value = str_replace('\\\n', '\n',	$value);
                                        $value = str_replace('\\\r', '\r',	$value);
                                        $value = str_replace('\\\t', '\t',	$value);

                                        $values .= '\'' . $value . '\', ';
                                }

                                $output .= 'INSERT INTO `oc_' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
                        }

                        $output .= "\n\n";
		}

		return $output;
	}
}