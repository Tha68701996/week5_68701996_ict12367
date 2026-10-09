<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">รายชื่อพนักงาน</h2>
    
    <!-- ตารางแสดงข้อมูลลูกค้า -->
     <div class="text-end mb-3">
      <a href="/add_employee" class="btn btn-primary">Add+</a>
    </div>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ลำดับที่</th>        <!-- index -->
          <th>รหัสพนักงาน</th>     <!-- customer_id -->
          <th>ชื่อ</th>            <!-- firstName -->
          <th>นามสกุล</th>        <!-- lastName -->
          <th>เบอร์โทร</th>       <!-- phone -->
          <th>ชื่อผู้ใช้</th>      <!-- username -->
          <th>จัดการ</th>
        </tr>
      </thead>

      <tbody>
        <!-- วนลูปข้อมูล customers -->
        <tr v-for="(item,index) in employee" :key="item.emp_id">
          <td>{{ index + 1 }}</td>       <!-- แสดงลำดับที่ (เริ่มจาก 1) -->
          <td>{{ item.emp_id }}</td> <!-- รหัสลูกค้า -->
          <td>{{ item.firstName }}</td>   <!-- ชื่อ -->
          <td>{{ item.lastName }}</td>    <!-- นามสกุล -->
          <td>{{ item.phone }}</td>       <!-- เบอร์โทร -->
          <td>{{ item.username }}</td>    <!-- ชื่อผู้ใช้ -->
          <td>
            <button class="btn btn-danger btn-sm" :disabled="deletingId === item.emp_id" @click="deleteEmployee(item)">
              {{ deletingId === item.emp_id ? "กำลังลบ..." : "ลบ" }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Loading: แสดงระหว่างรอข้อมูล -->
    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Error: แสดงเมื่อเกิดข้อผิดพลาด -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>
  </div>
</template>

<script>
// import ฟังก์ชันจาก Vue (Composition API)
import { ref, onMounted } from "vue";

export default {
  name: "EmployeeList", // ชื่อ component

  setup() {
    // -----------------------------
    // state (ตัวแปร reactive)
    // -----------------------------
    const employee = ref([]); // เก็บข้อมูลลูกค้า (array)
    const loading = ref(true); // สถานะโหลดข้อมูล
    const error = ref(null);   // เก็บ error
    const deletingId = ref(null);

    // -----------------------------
    // ฟังก์ชันดึงข้อมูลจาก API
    // -----------------------------
    const fetchdata = async () => {
      try {
        // เรียก API (PHP)
        const response = await fetch("http://localhost/week5_68701996_ict12367/php_api/show_emp.php");

        // ตรวจสอบว่าการเรียกสำเร็จหรือไม่
        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        // แปลง response เป็น JSON
        employee.value = await response.json();

      } catch (err) {
        // ถ้า error ให้เก็บข้อความไว้แสดง
        error.value = err.message;

      } finally {
        // ไม่ว่าจะสำเร็จหรือ error ให้หยุด loading
        loading.value = false;
      }
    };

    const deleteEmployee = async (item) => {
      if (!window.confirm(`ต้องการลบพนักงาน ${item.firstName} ${item.lastName} หรือไม่?`)) {
        return;
      }

      deletingId.value = item.emp_id;
      error.value = null;

      try {
        const response = await fetch("http://localhost/week5_68701996_ict12367/php_api/delete_employee.php", {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ emp_id: item.emp_id })
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
          throw new Error(result.message || "ไม่สามารถลบข้อมูลได้");
        }

        employee.value = employee.value.filter((employeeItem) => employeeItem.emp_id !== item.emp_id);
      } catch (err) {
        error.value = err.message;
      } finally {
        deletingId.value = null;
      }
    };

    // -----------------------------
    // lifecycle: ทำงานเมื่อ component โหลดเสร็จ
    // -----------------------------
    onMounted(() => {
      fetchdata(); // เรียก API ทันที
    });

    // -----------------------------
    // return ค่าไปใช้ใน template
    // -----------------------------
    return {
      employee,
      loading,
      error,
      deletingId,
      deleteEmployee
    };
  }
};
</script>